.PHONY: up down down-v sh game composer install test cs fix stan jwt-keys db-migrate lint lint-fix ci postman smoke ship-check

lint: cs stan lint-symfony test

lint-fix: fix lint

ci: install lint

ship-check: ci smoke postman

postman:
	@command -v npx >/dev/null 2>&1 || { echo "Install Node.js (npx) for Newman"; exit 1; }
	npx --yes newman run postman/games-cards.postman_collection.json --reporters cli

smoke:
	@chmod +x scripts/smoke.sh
	./scripts/smoke.sh $(or $(BASE_URL),http://localhost:8080)

lint-symfony:
	docker compose exec -T php php bin/console lint:container
	docker compose exec -T php php bin/console lint:yaml config/
	docker compose exec -T php php bin/console lint:twig templates/ 2>/dev/null || true

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
	docker compose exec -T php env APP_ENV=test APP_DEBUG=1 ./vendor/bin/phpunit --testdox --no-coverage

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
