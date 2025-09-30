# News Aggregator API

A Laravel-based news aggregation API that fetches and stores articles from multiple news sources including The Guardian, News API.org, and NY Times. The API provides endpoints for retrieving filtered and paginated news articles.

## Features

- **Multi-Source Aggregation**: Fetches news from The Guardian, News API.org, and NY Times
- **RESTful API**: Clean API endpoints for retrieving articles
- **Advanced Filtering**: Filter by date, category, source, and search keywords
- **Pagination**: Efficient pagination for large datasets
- **Scheduled Fetching**: Automated news fetching via Laravel scheduler
- **Docker Support**: Fully containerized application
- **PostgreSQL Database**: Reliable data storage

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: PostgreSQL 16
- **Web Server**: Nginx
- **Containerization**: Docker & Docker Compose

## Installation & Setup

### Prerequisites

- Docker & Docker Compose
- Git

### Step 1: Clone the Repository

```bash
git clone https://github.com/davidekechi/news-api.git
cd news-api
```

### Step 2: Start Docker Services

```bash
docker compose up -d --build
```

This will start the following services:
- `news-aggregator`: Main Laravel application
- `nginx-server`: Web server
- `postgres-db`: PostgreSQL database

### Step 3: Access the Application Container

```bash
docker exec -it news-aggregator bash
```

### Step 4: Environment Configuration

If `.env` file wasn't copied automatically:

```bash
cp .env.example .env
```

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

### Step 6: Run Database Migrations

```bash
php artisan migrate
```

### Step 7: Seed Database

```bash
php artisan db:seed
```

This will seed the sources (Guardian, News API.org, NY Times) and categories.

### Step 8: Configure News API Keys

You need to register with the following news services and obtain API keys:

1. **The Guardian**: [Register here](https://bonobo.capi.gutools.co.uk/register/developer)
2. **News API.org**: [Register here](https://newsapi.org/register)
3. **NY Times**: [Register here](https://developer.nytimes.com/get-started)

Add your API keys to the `.env` file:

```bash
NEWS_API_ORG_KEY=your_newsapi_key_here
GUARDIAN_API_KEY=your_guardian_key_here
NYTIMES_API_KEY=your_nytimes_key_here
```

### Step 9: Configure Article Fetching

#### For Local Development

Update `routes/console.php` to fetch articles every minute:

```php
Schedule::command('app:fetch-and-store-news-articles')->everyMinute();
```

Then run the scheduler:

```bash
php artisan schedule:run
```

#### For Production

Keep the default hourly schedule:

```php
Schedule::command('app:fetch-and-store-news-articles')->hourly();
```

Set up a cron job on your server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## API Endpoints

### Base URL
- Local: `http://localhost`
- The API runs on port 80 by default

### Article Endpoints

#### Get Articles
```http
GET /articles
```

**Query Parameters:**
- `query` - Search in title, author and content
- `from_date` - Filter by publication date (YYYY-MM-DD)
- `to_date` - Filter by publication date (YYYY-MM-DD)
- `category` - Filter by category name
- `source` - Filter by source name
- `preferredSources` - Comma-separated list of preferred sources
- `preferredCategories` - Comma-separated list of preferred categories
- `preferredAuthors` - Comma-separated list of preferred authors
- `per_page` - Items per page (default: 15)

**Example:**
```http
GET /articles?query=technology&category=tech&per_page=20
```

#### Get Filters
```http
GET /filters
```

Returns available categories and sources for filtering.

### Response Format

All API responses follow this format:

```json
{
    "status": "success",
    "message": "Request successful",
    "data": {
        "data": [...],
        "meta": {
            "current_page": 1,
            "last_page": 10,
            "per_page": 10,
            "total": 100
        }
    }
}
```

## Services

### News Sources

The application uses a service-based architecture for news fetching:

- `GuardianService` - Fetches from The Guardian API
- `NewsApiOrgService` - Fetches from News API.org
- `NYTimesService` - Fetches from NY Times API
- `NewsSourcesAggregator` - Orchestrates all news sources
- `BaseNewsSource` - Abstract base class for news sources
- `FetchAndStoreNewsArticles` - Artisan command to fetch and store articles

### Command

```bash
php artisan app:fetch-and-store-news-articles
```

This command fetches articles from all configured news sources.

## Development

### Accessing Services

- **Application**: http://localhost
- **Database**: localhost:5432 (from host machine)

### Container Names

- `news-aggregator` - Main Laravel application
- `nginx-server` - Nginx web server
- `postgres-db` - PostgreSQL database

### Useful Commands

```bash
# Access application container
docker exec -it news-aggregator bash

# View logs
docker compose logs -f app

# Restart services
docker compose restart

# Stop services
docker compose down

# Rebuild containers
docker compose up -d --build
```

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure proper database credentials
4. Set up SSL certificates
5. Configure cron job for scheduled tasks
6. Set up proper logging and monitoring

## Troubleshooting

### Common Issues

**1. Permission Issues**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

**2. Database Connection Issues**
- Ensure PostgreSQL service is running
- Check database credentials in `.env`
- Verify network connectivity between containers

**3. Scheduler Not Running**
```bash
# Check if scheduler is configured
php artisan schedule:list

# Run scheduler manually
php artisan schedule:run
```

**4. Package Installation Issues**
```bash
composer install
``` or 
```bash
composer update
```

## PSR Standards
The codebase adheres to PSR-12 coding standards. Please ensure any contributions also follow these standards.
Run the following command to check code style:

```bash
make stan
make fix
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For issues and questions, please create an issue on the GitHub repository.
