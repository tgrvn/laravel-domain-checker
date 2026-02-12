up:
	docker compose up -d

build:
	docker compose up -d --build

down:
	docker compose down

php:
	docker compose exec php bash

nginx:
	docker compose exec nginx sh

postgres:
	docker compose exec postgres psql -U laravel -d laravel
