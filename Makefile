.PHONY: help build up down restart install migrate entity cache-clear logs

help: ## Show this help
	@echo "PawTrack API - Development Commands"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Build Docker containers
	docker-compose build

up: ## Start Docker containers
	docker-compose up -d

down: ## Stop Docker containers
	docker-compose down

restart: down up ## Restart Docker containers

install: ## Install Composer dependencies
	docker-compose exec php composer install

migrate: ## Run database migrations
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

entity: ## Create a new entity (interactive)
	docker-compose exec php php bin/console make:entity

cache-clear: ## Clear Symfony cache
	docker-compose exec php php bin/console cache:clear

logs: ## Show Docker logs
	docker-compose logs -f

composer: ## Run composer command (use: make composer CMD="require package/name")
	docker-compose exec php composer $(CMD)

console: ## Run Symfony console command (use: make console CMD="list")
	docker-compose exec php php bin/console $(CMD)

bash: ## Enter PHP container shell
	docker-compose exec php sh

db-create: ## Create database
	docker-compose exec php php bin/console doctrine:database:create --if-not-exists

db-drop: ## Drop database
	docker-compose exec php php bin/console doctrine:database:drop --force --if-exists

db-reset: db-drop db-create migrate ## Reset database (drop, create, migrate)

jwt-generate: ## Generate JWT keys
	docker-compose exec php sh -c "mkdir -p config/jwt && \
		openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096 && \
		openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem && \
		chmod 644 config/jwt/private.pem config/jwt/public.pem"

setup: build up install jwt-generate db-create migrate ## Initial setup (build, start, install, create DB, migrate)

create-admin: ## Create an admin user (interactive)
	docker-compose exec php php bin/console app:create-admin

