# Clinic Appointment System

A REST API system built with Laravel 11 for managing clinic appointments and services.

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer

## Installation

1. Clone the repository
```bash
git clone https://github.com/razisek/jogjacamp-clinic
cd jogjacamp-clinic
```

2. Install dependencies
```bash
composer install
```

3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinic
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations
```bash
php artisan migrate
```

6. Start the queue worker
```bash
php artisan queue:work database
```

7. Generate Swagger documentation
```bash
php artisan l5-swagger:generate
```

8. Start the development server
```bash
php artisan serve
```

## API Documentation

Access the Swagger documentation at: `http://localhost:8000/api/documentation`

## Testing

Run the test suite:
```bash
php artisan test
```

## Queue Processing

The application uses Laravel's database queue driver. Make sure to keep the queue worker running:
```bash
php artisan queue:work database
```