# News Aggregator Backend

A Laravel-based backend for a news aggregator website. This project fetches articles from multiple news sources, stores them in a local database, and provides a flexible API for frontend consumption.

---

## Features
- Aggregates news from at least 3 sources (The Guardian, NewsAPI, New York Times)
- Stores articles, categories, and sources in a relational database
- Regularly updates articles using scheduled jobs
- RESTful API for retrieving articles with search, filtering, and pagination
- Clean, maintainable code following SOLID principles

---

## Data Sources
- **The Guardian** ([API Docs](https://open-platform.theguardian.com/documentation/))
- **NewsAPI** ([API Docs](https://newsapi.org/docs/endpoints/top-headlines))
- **New York Times** ([API Docs](https://developer.nytimes.com/docs/top-stories-product/1/overview))

---

## API Usage

### Get Articles
`GET /api/articles`

#### Query Parameters
- `search` — Search by title or content
- `source` — Filter by source name
- `category` — Filter by category name
- `author` — Filter by author
- `date_from` — Filter articles published after this date (YYYY-MM-DD)
- `date_to` — Filter articles published before this date (YYYY-MM-DD)
- Pagination: `page` (default: 1)

#### Example Request
```
GET /api/articles?search=climate&source=The Guardian&category=World&page=2
```

#### Example Response
```
{
  "current_page": 2,
  "data": [
    {
      "id": 12,
      "title": "Climate Change and Policy",
      "description": "...",
      "author": "Jane Doe",
      "url": "https://...",
      "urlToImage": "https://...",
      "published_at": "2025-07-22 10:00:00",
      "source_id": 1,
      "category_id": 3
    },
    ...
  ],
  "last_page": 5,
  ...
}
```

---

## Setup & Deployment

1. **Clone the repository**
2. **Install dependencies**
   ```
   composer install
   npm install && npm run build
   ```
3. **Configure environment**
   - Copy `.env.example` to `.env` and set your DB and API keys
4. **Run migrations and seeders**
   ```
   php artisan migrate --seed
   ```
5. **Set up Laravel scheduler**
   - Add this cron job to your server:
     ```
     * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
     ```
6. **(Optional) Start queue worker**
   ```
   php artisan queue:work
   ```

---

## Scheduler & Jobs
- **Jobs:** `FetchGuardianJob`, `FetchNewsApiJob`, `FetchNYTJob`
- **Scheduling:** Defined in `app/Console/Kernel.php` (default: hourly)
- **How it works:**
  - Each job fetches articles from its source and stores/updates them in the database.
  - The scheduler triggers these jobs automatically.

---

## Extending the Project
- **Add a new source:**
  1. Create a new Job class in `app/Jobs/`
  2. Implement the fetching logic
  3. Schedule the job in `app/Console/Kernel.php`
- **Add new filters:**
  - Update `ArticleController@index` to support additional query parameters

---

## SOLID Principles in the Codebase
- **Single Responsibility:** Each job fetches from one source; controllers handle API logic only.
- **Open/Closed:** Add new sources or filters without modifying existing code.
- **Liskov Substitution:** All jobs implement the same interface and can be scheduled interchangeably.
- **Interface Segregation:** Jobs, controllers, and models have focused, minimal interfaces.
- **Dependency Inversion:** External dependencies (API keys, HTTP clients) are injected/configured, not hardcoded.

---

## License
MIT
