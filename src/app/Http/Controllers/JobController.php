<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Job;

class JobController extends Controller
{
    public function index()
    {
        $last_updated = Cache::get('last_scrape_time');

        $jobs = Job::orderBy('posting_date', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->get();

        return view('jobs', [
            'jobs' => $jobs,
            'last_updated' => $last_updated
        ]);
    }
}