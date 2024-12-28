docker-build:
	docker build -t base-worker ./Builds/PHP
compose-build:
	docker compose build

compose-run:
	docker compose up -d

docker-logs:
	docker compose compose logs -f

country-sh:
	docker compose exec country bash

capital-sh:
	docker compose exec capital bash

test:
	docker compose exec capital vendor/phpunit/phpunit/phpunit && docker compose exec country vendor/phpunit/phpunit/phpunit

send-test:
	docker compose exec country bin/console worker:test:one fr

send-test-big:
	docker compose exec country bin/console worker:test:big