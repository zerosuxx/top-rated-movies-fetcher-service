run_docker=docker-compose run --rm app
exec_docker=docker-compose exec app

default: help

help: ## Show this help
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' -e 's/:.*#/ #/'

env:
	cp .env.example .env

build:
	docker-compose build

up-db:
	docker-compose up -d mysql

install:
	$(run_docker) composer install

migrate:
	$(run_docker) php artisan migrate:refresh

down:
	docker-compose down

test:
	$(run_docker) composer test

cat:
	$(run_docker) composer cat

migrate-db:
	$(run_docker) php artisan migrate

fetch-top-rated-movies:
	$(run_docker) php artisan fetch:top-rated-movies

sh:
	$(run_docker) sh

logs:
	docker-compose logs -f
