up: docker-up
init: docker-down-clear docker-pull docker-build docker-up app-start
bash:
	docker-compose run --rm php-cli bash

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

composer-install:
	docker-compose run --rm php-cli composer install

composer-require:
	docker-compose run --rm php-cli composer require ${ARGS}

test:
	docker-compose run --rm php-cli php bin/phpunit

app-start: composer-install app-assets-install assets-build app-db-create app-migrations

app-assets-install:
	docker-compose run --rm node yarn install

assets-watch:
	docker-compose run --rm node yarn run watch

assets-build:
	docker-compose run --rm node yarn run dev

app-db-create:
	docker-compose run --rm php-cli php bin/console doctrine:database:create --no-interaction --if-not-exists

app-migrations:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

app-cache-clear:
	docker-compose run --rm php-cli php bin/console cache:clear

