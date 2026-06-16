# Games Cards

Symfony 6.4 card-dealing API with JWT authentication, Bootstrap demo UI, and clean architecture — fully containerized with Docker.

## Features

- **Random dealing** from a standard 52-card deck
- **Custom sorting** by random suit and rank order
- **JWT API** — login, refresh token, protected `/cards` and `/api/hands/deal`
- **Web demo** at `/demo` and OpenAPI docs at `/api/doc`
- **CLI** `app:deal-cards` for terminal testing
- **Clean architecture** — Domain, Application, Infrastructure, UI

## Stack

| Component | Version |
|-----------|---------|
| PHP | 8.2 (Docker) |
| Symfony | 6.4 |
| Web server | Nginx 1.25 + PHP-FPM |
| Tests | PHPUnit 11 |
| Quality | PHPStan 2, PHP-CS-Fixer |

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and Docker Compose
- WSL2 supported on Windows

## Quick start

```bash
make up
make install
# → http://localhost:8080/demo
```

CLI:

```bash
make game
```

## Makefile commands

| Command | Description |
|---------|-------------|
| `make up` | Build and start containers (PHP-FPM + Nginx) |
| `make down` | Stop containers |
| `make install` | `composer install`, JWT keys, migrations |
| `make game` | Run CLI `app:deal-cards` |
| `make test` | Run PHPUnit suite |
| `make stan` | PHPStan static analysis |
| `make cs` | Code style check (dry-run) |
| `make fix` | Auto-fix code style |
| `make quality` | `test` + `stan` + `cs` (README quality gates) |
| `make ci` | `install` + `quality` (same as GitHub Actions CI) |
| `make sh` | Shell into PHP container |

## Code quality

Run the same gates locally as CI:

```bash
make install
make test    # Unit + functional tests (PHPUnit)
make stan    # Static analysis (PHPStan level 6 + Symfony extension)
make cs      # Code style check (PHP-CS-Fixer dry-run)
make fix     # Auto-fix style issues
```

Or in one step after install:

```bash
make quality    # test + stan + cs
make ci         # install + quality (mirrors GitHub Actions)
```

PHPUnit uses `failOnDeprecation=true`. Coverage reports go to `var/coverage/` when Xdebug or PCOV is available.

## CI (GitHub Actions)

Workflow `.github/workflows/quality.yml` runs on every push and pull request to `main`:

1. `make install`
2. `make test`
3. `make stan`
4. `make cs`

## Configuration

Copy `.env.example` to `.env.local` for local overrides:

```bash
cp .env.example .env.local
```

Never commit real secrets. Use `.env.local` (gitignored) for personal values.
Production logins require `API_PASSWORD_HASH`, generated with
`php bin/console security:hash-password --env=prod --no-debug`, rather than a
plaintext password.

For local Docker overrides, copy `docker-compose.override.dist.yml` to `docker-compose.override.yml`.

## Project structure

```
src/
├── Domain/           # Card, Hand, Suit, Rank
├── Application/      # HandDealer, HandSorter, HandDealingService, …
├── Infrastructure/   # PhpRandomizer, …
└── UI/
    ├── Http/         # Controllers, DTOs (JWT API)
    └── Console/      # DealCardsCommand
config/
├── routes/
└── packages/
tests/
├── Unit/
└── Functional/
postman/              # API collection
docker/
├── php/Dockerfile
└── nginx/
```

## API

- OpenAPI: http://localhost:8080/api/doc
- Postman: `postman/games-cards.postman_collection.json`

See `AGENTS.md`, `DEPLOY.md`, and `CHANGELOG.md` for team workflow and releases.

## License

Proprietary — internal / demonstration use.
