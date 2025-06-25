# h-da Job Scraper & Notifier

A fully automated, containerized Laravel application to scrape, store, display, and notify about new jobs on the h-da.de university job portal.

## Key Features:

- Automated Scraping: A Laravel Artisan command runs on a schedule to fetch the latest job data.
- Data Persistence: Scraped jobs are stored in a MySQL database.
- Web Interface: A simple Nginx + Laravel frontend displays the current job list, sorted by posting date.
- Email Notifications: Automatically sends an email alert when new jobs are discovered.
- Containerized with Docker: The entire stack (PHP-FPM, Nginx, MySQL, Scheduler) is managed with Docker Compose for one-command deployment on any VPS.

## Usage

### Clone the Repository

```
$ git clone https://github.com/pankrashin/h_da-job-scraper.git
cd h_da-job-scraper
```

### Edit .env

```
$ vim /src/.env
```

### Run Docker Compose

```
$ docker-compose up --build -d
```

### Generate Application Key

```
$ docker-compose exec app php artisan key:generate
```

### Clear Caches

```
$ docker-compose exec app php artisan config:clear
$ docker-compose exec app php artisan route:clear
$ docker-compose exec app php artisan view:clear
```

### Run the Database Migration

```
$ docker-compose exec app php artisan migrate
```

### Run the Scraper Manually (Initial Population)

```
$ docker-compose exec app php artisan jobs:scrape
```
