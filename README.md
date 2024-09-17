# Affiliate Locator

Affiliate Locator is a Laravel-based application that processes a list of affiliates and identifies those within 100km of the Dublin office. It provides both a command-line interface and a web API for accessing this functionality.

## Features

- Read and parse affiliate data from a text file
- Calculate distances using the Haversine formula
- Filter affiliates within 100km of the Dublin office
- Sort results by Affiliate ID
- Provide results via CLI and web API
- Efficient handling of large datasets through streaming

## Requirements

- Docker
- Docker Compose

## Installation and Setup

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/affiliate-locator.git
   cd affiliate-locator
   ```

2. Copy the `.env.example` file to `.env`:
   ```
   cp .env.example .env
   ```

3. Build and start the Docker containers:
   ```
   docker-compose up -d --build
   ```

4. Install PHP dependencies:
   ```
   docker-compose exec app composer install
   ```

5. Generate an application key:
   ```
   docker-compose exec app php artisan key:generate
   ```

6. Install JavaScript dependencies and compile assets:
   ```
   docker-compose exec app npm install
   docker-compose exec app npm run dev
   ```

The application should now be running at `http://localhost:8000`.

## Usage

### Command Line Interface

To run the affiliate filter from the command line:

```
docker-compose exec app php artisan affiliates:filter
```

This will output the list of affiliates within 100km of the Dublin office, sorted by Affiliate ID.

### Web API

The application provides two API endpoints:

1. Get all affiliates:
   ```
   GET http://localhost:8000/api/affiliates
   ```

2. Get filtered affiliates (within 100km of Dublin office):
   ```
   GET http://localhost:8000/api/affiliates/filtered
   ```

### Frontend

The application provides two routes:

1. Get all affiliates:
   ```
   GET http://localhost:8000/affiliates
   ```

2. Get filtered affiliates (within 100km of Dublin office):
   ```
   GET http://localhost:8000/affiliates/filtered
   ```

## Project Structure

- `app/Console/Commands/FilterAffiliates.php`: CLI command for filtering affiliates
- `app/Http/Controllers/AffiliateController.php`: Controller for API endpoints
- `app/Services/FileReaderService.php`: Service for reading the affiliate file
- `app/Services/AffiliateFilterService.php`: Service for filtering affiliates
- `app/DTOs/AffiliateDTO.php`: Data Transfer Object for affiliate data
- `resources/js/components/AffiliateList.vue`: Vue component for displaying affiliates
- - `resources/js/components/FilteredAffiliateList.vue`: Vue component for displaying filtered affiliates
- `docker-compose.yml`: Docker Compose configuration
- `Dockerfile`: Docker configuration for the app container

## Testing

To run the test suite:

```
docker-compose exec app php artisan test
```

## Development

To watch for changes and recompile assets during development:

```
docker-compose exec app npm run watch
```

## Stopping the Application

To stop the Docker containers:

```
docker-compose down
```

## Troubleshooting

If you encounter any issues with file permissions when using Docker, you may need to adjust the permissions on your host machine:

```
sudo chown -R $USER:$USER .
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
