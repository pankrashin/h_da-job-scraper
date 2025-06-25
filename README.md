# h-da Job Scraper & Notifier

A fully automated, containerized Laravel application to scrape, store, display, and notify about new jobs on the h-da.de university job portal.

## Key Features:

- Automated Scraping: A Laravel Artisan command runs on a schedule to fetch the latest job data.
- Data Persistence: Scraped jobs are stored in a MySQL database.
- Web Interface: A simple Nginx + Laravel frontend displays the current job list, sorted by posting date.
- Email Notifications: Automatically sends an email alert when new jobs are discovered.
- Containerized with Docker: The entire stack (PHP-FPM, Nginx, MySQL, Scheduler) is managed with Docker Compose for one-command deployment on any VPS.
