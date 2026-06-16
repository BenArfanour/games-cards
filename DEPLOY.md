# Deploy — games-cards

Symfony 6.4 JWT API. Docker Compose (PHP-FPM + Nginx + Postgres). Local port **8080**.

## Prerequisites

- Docker and Docker Compose on the target host
- Git access to this repository
- Production secrets **not** in git (see below)

## Environment variables (production)

Set on the host or in `.env.local` (never commit):

| Variable | Example | Notes |
|----------|---------|--------|
| `APP_ENV` | `prod` | Required |
| `APP_DEBUG` | `0` | Required |
| `APP_SECRET` | random 32+ chars | `openssl rand -hex 32` |
| `DATABASE_URL` | `postgresql://...` | Prod Postgres (or keep SQLite only for demos) |
| `API_USER` | `api_user` | Login username for smoke scripts |
| `API_PASSWORD_HASH` | hashed strong password | Required by prod login; **not** plaintext |
| `JWT_PASSPHRASE` | random string | Lexik key encryption |
| `JWT_SECRET_KEY` | `%kernel.project_dir%/config/jwt/private.pem` | Paths on server |
| `JWT_PUBLIC_KEY` | `%kernel.project_dir%/config/jwt/public.pem` | |

Generate the production API password hash before editing `.env.local`:

```bash
docker compose exec -T php php bin/console security:hash-password --env=prod --no-debug
```

Set `API_PASSWORD_HASH` to the generated hash. Quote the value in `.env.local`
because Symfony password hashes contain `$` characters:

```dotenv
API_PASSWORD_HASH='$2y$13$...'
```

Generate JWT keys once on the server:

```bash
docker compose exec -T php php bin/console lexik:jwt:generate-keypair --skip-if-exists
```

## First deploy (new server)

```bash
git clone <repo-url> games-cards && cd games-cards
cp .env .env.local   # edit with prod values
make up
make install       # composer + jwt keys + migrations
make ci            # verify quality gates locally
curl -sf http://localhost:8080/demo
```

## Release deploy (update)

Run after merging to `main` and **CI green** on GitHub.

```bash
cd /path/to/games-cards
git pull origin main
make up            # rebuild if Dockerfile changed
make install       # composer + keys + migrations
make ci            # optional but recommended before switching traffic
```

### Production Symfony commands

```bash
docker compose exec -T php php bin/console doctrine:migrations:migrate --no-interaction --env=prod --no-debug
docker compose exec -T php php bin/console cache:clear --env=prod --no-debug
docker compose exec -T php php bin/console cache:warmup --env=prod --no-debug
```

## Smoke tests (after deploy)

```bash
BASE=http://localhost:8080   # or your staging/prod URL

curl -sf "$BASE/demo"
curl -sf "$BASE/api/doc.json" | head -c 200

# Login
TOKEN=$(curl -s -X POST "$BASE/api/login_check" \
  -H 'Content-Type: application/json' \
  -d '{"username":"api_user","password":"YOUR_PASSWORD"}' | jq -r .token)

curl -sf -H "Authorization: Bearer $TOKEN" "$BASE/cards"
curl -sf -X POST -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"count":5}' "$BASE/api/hands/deal"
```

Expected: HTTP 200, JSON responses, 401 without token.

## Migrations in this release

| Migration | Description | Risk |
|-----------|-------------|------|
| `Version20260614150000` | Creates `refresh_tokens` table | Low — additive only |

**Rollback:** `down()` drops `refresh_tokens` (loses refresh tokens, not access tokens until expiry).

**Backup:** snapshot Postgres (or copy SQLite file) before migrate on prod.

## Rollback

1. `git checkout <previous-tag-or-commit>`
2. `make up && make install`
3. If migration was applied and is reversible:  
   `docker compose exec -T php php bin/console doctrine:migrations:migrate prev --no-interaction --env=prod`
4. If not reversible: restore DB from backup
5. Re-run smoke tests

## Release

After **`/ship`** confirms CI green on GitHub:

```bash
git pull origin main
git tag v1.1.0
git push origin v1.1.0
```

GitHub Actions **Release** workflow creates the GitHub Release with auto-generated notes. Update `CHANGELOG.md` before tagging.

## CI

GitHub Actions runs `make ci` on push/PR to `main`.

During **`/ship`**, the agent uses **GitHub MCP** to verify:
- PR check runs (`pull_request_read` → `get_check_runs`)
- Commits and changed files (`get_commit`, `list_commits`)
- Latest release / tags (`get_latest_release`, `list_tags`)

Still run **`make ci` locally** before ship. MCP cannot push git commits or deploy to your server.

## Cursor ship checklist

Before production, run in Cursor: **`/ship`**

Phases: CI green → migration review → secrets → staging smoke → prod deploy (explicit confirm) → post-deploy verify → rollback plan.
