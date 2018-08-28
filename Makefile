# Load .env file if it exists

CURRENT_DIRECTORY := $(shell pwd)

help:
	@echo "Docker Compose Help"
	@echo "-----------------------"
	@echo ""
	@echo "If tests pass, add fixture data and start up the api:"
	@echo "    make start"
	@echo ""
	@echo "Really, really start over:"
	@echo "    make clean"
	@echo ""
	@echo "See contents of Makefile for more targets."

up:
	@docker-compose up -d

stop:
	@docker-compose stop

restart: stop up

build:
	@docker-compose up -d --build

tail:
	@docker-compose logs -f

php:
	@docker-compose exec php-fpm bash

test:
	docker exec -ti docker-kafka-connector-php-fpm ./vendor/phpunit/phpunit/phpunit tests

.PHONY: up stop restart build tail php redis test test-coverage