.PHONY: help dev-up dev-down dev-build dev-logs dev-php prod-up prod-down prod-logs prod-php fix-permissions

# Định nghĩa mã màu ANSI
CLR_RESET   := \033[0m
CLR_BOLD    := \033[1m
CLR_CYAN    := \033[36m
CLR_GREEN   := \033[32m
CLR_YELLOW  := \033[33m
CLR_MAGENTA := \033[35m

# Override when needed, for example:
# make fix-permissions COMPOSE_FILE=docker-compose.yml
COMPOSE_FILE ?= docker-compose.dev.yml
APP_SERVICE  ?= app
PHP_FPM_USER ?= www-data

help:
	@printf "$(CLR_BOLD)$(CLR_MAGENTA)Available Commands:$(CLR_RESET)\n\n"
	@printf "$(CLR_BOLD)$(CLR_CYAN)[Development Stack]$(CLR_RESET)\n"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "dev-up"           "Start development stack"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "dev-down"         "Stop development stack"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "dev-build"        "Build or rebuild development images (use c=\"--no-cache\" if needed)"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "dev-logs"         "Follow logs for development stack"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "dev-php"          "Run command in dev container (e.g., make dev-php cmd=\"php artisan migrate\")"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "fix-permissions"  "Fix Laravel storage/cache permissions for PHP-FPM"
	@printf "\n"
	@printf "$(CLR_BOLD)$(CLR_YELLOW)[Production Stack]$(CLR_RESET)\n"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "prod-up"          "Start production stack"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "prod-down"        "Stop production stack"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "prod-logs"        "Follow logs for production stack"
	@printf "  $(CLR_GREEN)%-18s$(CLR_RESET) %s\n" "prod-php"         "Run command in prod container (e.g., make prod-php cmd=\"php artisan migrate\")"

# Development targets
dev-up:
	@printf "$(CLR_CYAN)▶ Starting development stack...$(CLR_RESET)\n"
	docker compose -f docker-compose.dev.yml up -d

dev-down:
	@printf "$(CLR_YELLOW)■ Stopping development stack...$(CLR_RESET)\n"
	docker compose -f docker-compose.dev.yml down

dev-build:
	@printf "$(CLR_CYAN)🔨 Building development images...$(CLR_RESET)\n"
	docker compose -f docker-compose.dev.yml build $(c)

dev-logs:
	docker compose -f docker-compose.dev.yml logs -f

dev-php:
	@if [ -z "$(cmd)" ]; then \
		printf "$(CLR_YELLOW)⚠ Missing 'cmd'. Example: make dev-php cmd=\"php artisan status\"$(CLR_RESET)\n"; \
		exit 1; \
	fi
	docker compose -f docker-compose.dev.yml exec app $(cmd)

fix-permissions:
	@printf "$(CLR_CYAN)🔧 Fixing Laravel writable directories for PHP-FPM...$(CLR_RESET)\n"
	docker compose -f $(COMPOSE_FILE) exec -T -u root $(APP_SERVICE) sh -lc '\
		mkdir -p /var/www/html/storage/logs /var/www/html/bootstrap/cache /var/www/html/infrastructure/Logs && \
		chown -R $(PHP_FPM_USER):$(PHP_FPM_USER) /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/infrastructure/Logs && \
		chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/infrastructure/Logs \
	'
	docker compose -f $(COMPOSE_FILE) exec -T -u $(PHP_FPM_USER) $(APP_SERVICE) sh -lc '\
		touch /var/www/html/storage/logs/.permission-check && \
		rm /var/www/html/storage/logs/.permission-check && \
		test -w /var/www/html/bootstrap/cache && \
		test -w /var/www/html/infrastructure/Logs \
	'
	@printf "$(CLR_GREEN)✔ Laravel storage, cache, and infrastructure logs are writable by $(PHP_FPM_USER).$(CLR_RESET)\n"

# Production targets
prod-up:
	@printf "$(CLR_MAGENTA)🚀 Starting production stack...$(CLR_RESET)\n"
	docker compose -f docker-compose.yml up -d

prod-down:
	@printf "$(CLR_YELLOW)■ Stopping production stack...$(CLR_RESET)\n"
	docker compose -f docker-compose.yml down

prod-logs:
	docker compose -f docker-compose.yml logs -f

prod-php:
	@if [ -z "$(cmd)" ]; then \
		printf "$(CLR_YELLOW)⚠ Missing 'cmd'. Example: make prod-php cmd=\"php artisan status\"$(CLR_RESET)\n"; \
		exit 1; \
	fi
	docker compose -f docker-compose.yml exec app $(cmd)
