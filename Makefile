docker-build:
	docker compose build

docker-run:
	docker compose up -d

docker-sh:
	docker compose exec challenge bash

docker-country:
	docker compose exec challenge php src/country.php

docker-capital:
	docker compose exec challenge php src/capital.php
