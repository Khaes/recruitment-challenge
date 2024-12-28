docker-build:
	docker compose build

docker-run:
	docker compose up -d

docker-sh:
	docker compose exec challenge bash

install:
	docker compose exec challenge composer install

test:
	docker compose exec challenge vendor/phpunit/phpunit/phpunit

docker-messenger:
	docker compose exec challenge bin/console messenger:consume async -vv

docker-test:
	docker compose exec challenge bin/console worker:test fr
