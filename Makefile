.PHONY: up down down-v sh game composer install test cs fix stan jwt-keys db-migrate

up:
	docker compose up -d --build

down:
	docker compose down

down-v:
	docker compose down -v

sh:
	docker compose exec php bash

game:
	docker compose exec php php bin/console app:deal-cards

composer:
	docker compose exec -T php composer $(cmd)

install:
	docker compose exec -T php composer install
	$(MAKE) jwt-keys
	$(MAKE) db-migrate

test:
	$(MAKE) db-migrate-test
	docker compose exec -T php env APP_ENV=test APP_DEBUG=1 ./vendor/bin/phpunit --testdox

cs:
	docker compose exec -T php ./vendor/bin/php-cs-fixer fix --diff --dry-run

fix:
	docker compose exec -T php ./vendor/bin/php-cs-fixer fix

stan:
	docker compose exec -T php ./vendor/bin/phpstan analyse -c phpstan.dist.neon --memory-limit=512M

jwt-keys:
	docker compose exec -T php php bin/console lexik:jwt:generate-keypair --skip-if-exists

db-migrate:
	docker compose exec -T php php bin/console doctrine:migrations:migrate --no-interaction

db-migrate-test:
	docker compose exec -T php php bin/console doctrine:migrations:migrate --no-interaction --env=test
