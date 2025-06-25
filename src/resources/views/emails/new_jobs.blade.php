<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Job Postings</title>
    <style>
        body { font-family: system-ui, sans-serif; line-height: 1.6; color: #333; }
        .job-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        h3 { margin-top: 0; }
        a { color: #2563eb; }
    </style>
</head>
<body>
    <h1>New Job Postings Found!</h1>
    <p>The scraper has discovered the following new job listings:</p>
    <hr>

    @foreach ($newJobs as $job)
        <div class="job-card">
            <h3><a href="{{ $job['link'] }}">{{ $job['title'] }}</a></h3>
            <p>
                <strong>Company:</strong> {{ $job['company'] }}<br>
                <strong>Location:</strong> {{ $job['location'] }}<br>
                {{-- --- THIS IS THE CORRECTED LINE --- --}}
                <strong>Date Posted:</strong> {{ \Carbon\Carbon::parse($job['posting_date'])->format('F j, Y') }}
            </p>
        </div>
    @endforeach
</body>
</html>