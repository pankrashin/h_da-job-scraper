<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewJobsNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $newJobs;

    /**
     * Create a new message instance.
     * @param array $newJobs
     * @return void
     */
    public function __construct(array $newJobs)
    {
        $this->newJobs = $newJobs;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(count($this->newJobs) . ' New Job(s) Found on University Portal!')
                    ->view('emails.new_jobs');
    }
}