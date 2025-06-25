<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>University Jobs</title>
    <style>
        body { font-family: system-ui, sans-serif; line-height: 1.6; background: #f4f4f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        h1, h2, h3 { text-align: center; color: #2c3e50; }
        .job-card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; display: flex; align-items: center; }
        .job-logo img { width: 90px; height: auto; margin-right: 20px; }
        .job-title { font-size: 1.25em; font-weight: bold; }
        .job-title a { text-decoration: none; color: #3498db; }
    </style>
</head>
<body>
    <div class="container">
        <h1>h_da Job Portal Scraper & Notifier</h1>
        <h2>by Daniil Pankrashin</h2>
        <h3>{{ count($jobs) }} Jobs Found</h3>
         @if ($last_updated)
            <p style="text-align: center; color: #718096; margin-top: -10px; margin-bottom: 20px;">
                Last updated: {{ $last_updated->format('F j, Y, H:i') }} UTC ({{ $last_updated->diffForHumans() }})
            </p>
        @endif
        
        @forelse ($jobs as $job)
            <div class="job-card">
                <div class="job-logo">
                    <img src="{{ $job->logo_url }}" alt="">
                </div>
                <div class="job-details">
                    <div class="job-title"><a href="{{ $job->link }}" target="_blank">{{ $job->title }}</a></div>
                    <div class="job-company">{{ $job->company }}</div>
                    <div class="job-meta">📍 {{ $job->location }} | 📅 {{ $job->posting_date->format('F j, Y') }}</div>
                </div>
            </div>
        @empty
            <p>No jobs found. Please run the scraper to populate the database.</p>
        @endforelse
    </div>
</body>
</html>