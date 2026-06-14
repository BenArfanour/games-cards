# Changelog

All notable changes to this project are documented here.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added

- GitHub Actions CI (`make ci`) — PHPStan, CS-Fixer, Symfony linters, PHPUnit
- Postman contract tests in CI (`make postman`)
- Smoke test script (`make smoke`, `scripts/smoke.sh`)
- Cursor team rules (`.cursor/rules/`, `AGENTS.md`)
- Deploy guide (`DEPLOY.md`)
- Release workflow on git tags (`v*`)
- Makefile targets: `lint`, `lint-fix`, `ci`, `postman`, `smoke`, `ship-check`

### Changed

- PHPUnit runs with `--no-coverage` in CI (no false failures without pcov)

## [1.0.0] — 2026-06-14

### Added

- JWT API (login, refresh, protected `/cards` and `/api/hands/deal`)
- Bootstrap demo UI at `/demo`
- OpenAPI docs (Nelmio)
- Postman collection
- Clean architecture (Domain / Application / Infrastructure / UI)
- Docker Compose local stack on port 8080

[Unreleased]: https://github.com/BenArfanour/games-cards/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/BenArfanour/games-cards/releases/tag/v1.0.0
