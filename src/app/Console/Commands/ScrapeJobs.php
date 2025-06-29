<?php

namespace App\Console\Commands;

// Laravel & System Imports
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

// App-specific Imports
use App\Mail\NewJobsNotification;
use App\Models\Job;

// Third-party Imports
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;

class ScrapeJobs extends Command
{
    protected $signature = 'jobs:scrape';
    protected $description = 'Synchronizes the local database with the university job portal.';

    public function handle()
    {
        $this->info('--- Starting database synchronization cycle ---');

        $liveJobs = $this->fetchAndHashLiveJobs();
        
        if (empty($liveJobs)) {
            $this->error('Scraper found no jobs from the website. Aborting cycle.');
            return Command::FAILURE;
        }
        $this->info("Scrape complete. Found " . count($liveJobs) . " live jobs on the portal.");

        $databaseJobHashes = Job::pluck('unique_hash')->all();
        $liveJobHashes = array_keys($liveJobs);
        
        $jobsToAddHashes = array_diff($liveJobHashes, $databaseJobHashes);
        $jobsToDeleteHashes = array_diff($databaseJobHashes, $liveJobHashes);

        $newlyCreatedJobs = $this->addNewJobs($jobsToAddHashes, $liveJobs);
        // $this->removeStaleJobs($jobsToDeleteHashes);
        $this->handleNotifications($newlyCreatedJobs);
        $this->updateLastScrapeTimestamp();

        $this->info("--- Synchronization cycle finished successfully. ---");
        return Command::SUCCESS;
    }

    /**
     * Fetches all jobs and returns them as an array keyed by their unique hash.
     */
    private function fetchAndHashLiveJobs(): array
    {
        $allJobs = [];
        $pageOffset = 0;
        $maxJsscLimit = 280;

        while ($pageOffset <= $maxJsscLimit) {
            $url = "https://h-da.al.sites.jobware.net/suchergebnis-praktika.html?jmyc=3&jssi=42685536465485516&jssc={$pageOffset}#hili";
            try {
                $response = Http::timeout(60)->get($url);
                if ($response->successful()) {
                    $crawler = new Crawler($response->body());
                    $crawler->filter('.jwtpl-hili-item')->each(function ($node) use (&$allJobs) {
                        
                        $dateString = $node->filter('.jwtpl-hili-itemDate')->text('N/A');
                        
                        try {
                            $formattedDate = Carbon::createFromFormat('d.m.Y', $dateString)->toDateString();
                        } catch (\Exception $e) {
                            $formattedDate = now()->toDateString();
                        }

                        $jobData = [
                            'title'        => $node->filter('.jwtpl-hili-itemTitel')->text('N/A'),
                            'company'      => $node->filter('.jwtpl-hili-itemCompany')->text('N/A'),
                            'location'     => $node->filter('.jwtpl-hili-itemLocation')->text('N/A'),
                            'posting_date' => $formattedDate,
                            'link'         => $node->filter('.jwtpl-hili-itemLink')->attr('href'),
                            'logo_url'     => $node->filter('.jwtpl-hili-col1 img')->attr('src'),
                        ];

                        $normalizedString = strtolower(trim($jobData['company']) . trim($jobData['title']) . trim($jobData['location']) . $formattedDate);
                        $hash = sha1($normalizedString);
                        $jobData['unique_hash'] = $hash;

                        $allJobs[$hash] = $jobData;
                    });
                }
            } catch (\Exception $e) {
                $this->warn("Warning: Failed to scrape page at offset {$pageOffset}. Error: " . $e->getMessage());
            }
            $pageOffset += 20;
            usleep(200000);
        }
        
        return $allJobs;
    }

    /**
     * Creates new jobs in the database.
     */
    private function addNewJobs(array $hashes, array $liveJobs): array
    {
        if (empty($hashes)) {
            return [];
        }

        $newlyCreatedJobs = [];
        foreach ($hashes as $hash) {
            $jobData = $liveJobs[$hash];
            Job::create($jobData);
            $newlyCreatedJobs[] = $jobData;
        }

        $this->info("Added " . count($newlyCreatedJobs) . " new jobs to the database.");
        return $newlyCreatedJobs;
    }

    /**
     * Deletes stale jobs from the database (currently unused because of unreliable work of h_da job portal)
     */
    private function removeStaleJobs(array $hashes): void
    {
        if (empty($hashes)) {
            $this->info("No stale jobs to remove.");
            return;
        }

        $count = Job::whereIn('unique_hash', $hashes)->delete();
        $this->info("Removed {$count} stale jobs from the database.");
    }

    /**
     * Sends an email notification if there are new jobs.
     */
    private function handleNotifications(array $newlyCreatedJobs): void
    {
        if (count($newlyCreatedJobs) > 0) {
            $this->info("Found new jobs. Preparing to send notification...");
            try {
                $recipient = config('mail.recipient_address');
                if (!$recipient) {
                    $this->error("Mail recipient address is not configured."); return;
                }
                Mail::to($recipient)->send(new NewJobsNotification($newlyCreatedJobs));
                $this->info("Email notification sent successfully to {$recipient}.");
            } catch (\Exception $e) {
                $this->error("Failed to send email notification: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Updates the 'last_scrape_time' timestamp in the application cache.
     */
    private function updateLastScrapeTimestamp(): void
    {
        Cache::put('last_scrape_time', now());
        $this->info('Updated last scrape timestamp in cache.');
    }
}