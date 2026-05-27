# HelpDesk API

REST API for ticket management built with Laravel 13 and PostgreSQL.

## Stack

| | |
|---|---|
| PHP | 8.4 |
| Framework | Laravel 13 |
| Database | PostgreSQL 16 |
| Auth | Laravel Sanctum |
| Queue | Laravel Horizon + Redis |
| Permissions | Spatie Permission |

## Requirements

- PHP 8.3+
- PostgreSQL 16+
- Redis
- Composer

## Setup

```bash
git clone https://github.com/GuilhermeKAC/helpdesk-backend.git
cd helpdesk-backend

composer install
cp .env.example .env
php artisan key:generate

# Configure DB credentials in .env, then:
php artisan migrate
php artisan db:seed

php artisan serve
```

Default credentials after seed:

| Email | Password | Role |
|-------|----------|------|
| admin@helpdesk.com | password | admin |

## Testing

```bash
# Requires a helpdesk_test PostgreSQL database
php artisan test
```

## Docs

Full technical documentation in [`docs/`](./docs/README.md).

## License

MIT
