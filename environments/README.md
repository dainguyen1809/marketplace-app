# Environments

This directory contains Docker‑Compose based LEMP (Linux, Nginx, MySQL, PHP 8.4) environments for the Laravel project.

## Directory layout

```
Environments/
├── php/                 # PHP‑FPM Dockerfile (builds php:8.4-fpm-alpine with Laravel extensions)
├── nginx/
│   └── conf.d/          # Nginx site configuration (default.conf)
├── docker-compose.dev.yml   # Development stack (exposes port 8080)
├── docker-compose.yml       # Production‑ready stack (no exposed ports)
├── .env.docker.dev          # Development environment variables
├── .env.docker.prod         # Production environment variables (keep secret!)
├── Makefile                 # Helper commands to manage the stacks
└── README.md                # This file
```

## Quick start (development)

1. **Copy the development env file (if you haven’t already)**

    ```bash
    cp .env.docker.dev .env.docker.dev   # already present; edit if needed
    ```

2. **Start the stack**

    ```bash
    make dev-up
    # or: docker compose -f docker-compose.dev.yml up -d
    ```

3. **Verify**
    - PHP version: `make dev-php cmd="php -v"`
    - MySQL connection: open http://localhost:8080/test.php in a browser or `curl http://localhost:8080/test.php`
    - The application code is mounted at `/var/www/html` inside the containers, so your Laravel project (located one level up) is immediately available.

4. **Run Laravel commands**

    ```bash
    make dev-php cmd="composer install"
    make dev-php cmd="php artisan key:generate"
    make dev-php cmd="php artisan migrate"
    ```

5. **Follow logs**

    ```bash
    make dev-logs   # press Ctrl+C to stop
    ```

6. **Stop the development stack**
    ```bash
    make dev-down
    # add `-v` to also remove the named volume if you want a clean slate:
    # make dev-down && docker compose -f docker-compose.dev.yml down -v
    ```

## Production‑like stack

The `docker-compose.yml` file is intended for staging or production use. It does **not** expose ports directly; you should place a reverse proxy (Traefik, Nginx, Caddy, cloud load‑balancer, etc.) in front of the `web` service.

1. **Configure production secrets**  
   Edit `.env.docker.prod` (never commit this file) with strong passwords:

    ```dotenv
    MYSQL_DATABASE=laravel_prod
    MYSQL_ROOT_PASSWORD=⟨strong_root_password⟩
    MYSQL_PASSWORD=⟨strong_app_password⟩
    MYSQL_USER=laravel
    ```

2. **Start the stack**

    ```bash
    make prod-up
    # or: docker compose up -d
    ```

3. **Run commands (e.g., migrations)**

    ```bash
    make prod-php cmd="php artisan migrate"
    ```

4. **Stop**
    ```bash
    make prod-down
    ```

## Makefile reference

| Target                   | Description                                   |
| ------------------------ | --------------------------------------------- |
| `make help`              | Show available targets                        |
| `make dev-up`            | Start development stack                       |
| `make dev-down`          | Stop development stack                        |
| `make dev-logs`          | Follow logs of development stack              |
| `make dev-php cmd="…" `  | Run a command inside the app container (dev)  |
| `make prod-up`           | Start production stack                        |
| `make prod-down`         | Stop production stack                         |
| `make prod-logs`         | Follow logs of production stack               |
| `make prod-php cmd="…" ` | Run a command inside the app container (prod) |

## Notes

- The PHP image is built from `./php/Dockerfile` (Alpine‑based `php:8.4-fpm-alpine`) and includes the extensions Laravel requires: `pdo_mysql`, `mbstring`, `exif`, `pcntl`, `bcmath`, `gd`, `zip`, `redis`.
- Nginx serves `/var/www/html/public` and forwards `.php` requests to the PHP‑FPM service on port 9000.
- MySQL data persists in the named volume `db_data`.
- For IDE debugging with Xdebug, uncomment the relevant lines in `.env.docker.dev` and ensure your IDE is configured to listen on the specified host/port.
