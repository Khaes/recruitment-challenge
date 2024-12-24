docker-build:
	docker compose build

docker-run:
	docker compose up -d

docker-sh:
	docker compose exec challenge bash

install:
	docker compose exec challenge composer install

docker-country:
	docker compose exec challenge bin/console worker:country:consume

docker-capital:
	docker compose exec challenge bin/console worker:capital:consume

docker-test:
	docker compose exec challenge bin/console worker:test fr
