# Agents — games-cards

Symfony 6.4 JWT API (clean architecture). Docker on port **8080**.

## Delivery pipeline

```text
Build → make ci → /staff-review → /pr-ready → merge → /ship → tag → deploy
```

## Before every PR

```bash
make up
make ship-check    # ci + smoke + postman (full pre-PR gate)
/staff-review
/pr-ready
```

Or minimal: `make ci`

## CI (GitHub Actions + MCP)

| Workflow | Trigger | Runs |
|----------|---------|------|
| **Quality** | PR + push `main` | `make ci` + `make postman` + `make smoke` |
| **Release** | tag `v*` | GitHub Release with auto notes |

**`/ship` uses GitHub MCP** — CI check runs, commits, tags, releases.

## How we work

| Topic | Rule |
|-------|------|
| **TDD** | Only for **complex** features. Simple wiring: implement then test. |
| **Tests** | Unit + functional, all JWT scenarios before PR. |
| **JWT APIs** | Validate input; correct HTTP codes — never 500 for auth/validation. |
| **Layers** | Domain → Application → Infrastructure → UI. |

## Cursor commands

| Command | When |
|---------|------|
| `/lint-app` | Whole codebase lint |
| `/feature-lifecycle` | New feature end-to-end |
| `/pr-ready` | Before PR (GitHub MCP to open PR) |
| `/staff-review` | Architecture review |
| `/ship` | After merge — GitHub MCP + deploy checklist |

## Makefile targets

| Target | Purpose |
|--------|---------|
| `make ci` | Install + lint (matches CI core) |
| `make ship-check` | ci + smoke + postman |
| `make smoke` | curl smoke tests |
| `make postman` | Newman collection |

## Release

```bash
git tag v1.1.0 && git push origin v1.1.0
```

See `CHANGELOG.md` and `DEPLOY.md`.

## Rules

`.cursor/rules/` — team standards, JWT API, ship workflow.
