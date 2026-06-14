# Commit message template (games-cards)

Use this format for **every** commit — local `git commit` and GitHub MCP (`push_files`, `create_or_update_file`).

## Format

```
<type>(<topic>): <description> <ISSUE-ID>

<Why this change is needed — 1-3 sentences. Focus on intent, not file names.>

Changes:
- <Concrete change 1>
- <Concrete change 2>
```

### Subject line breakdown

| Part | Description | Examples |
|------|-------------|----------|
| **type** | Kind of change | `feat`, `fix`, `chore`, `docs`, `refactor`, `test`, `ci`, `build`, `perf`, `style` |
| **topic** | Area or module touched | `auth`, `cards`, `dealing`, `api`, `ci`, `docker` |
| **description** | Short imperative summary | `add hand deal endpoint` |
| **ISSUE-ID** | Ticket or GitHub issue at end | `#42`, `GC-12`, or user-provided ID |

**Full subject example:** `feat(cards): add hand deal endpoint #42`

## Type guide

| Type | When to use |
|------|-------------|
| `feat` | New feature or user-facing behaviour |
| `fix` | Bug fix |
| `chore` | Maintenance, tooling, deps |
| `docs` | Documentation only |
| `refactor` | Code restructure, same behaviour |
| `test` | Tests only |
| `ci` | CI/CD, GitHub Actions |
| `build` | Build system, composer, docker |
| `perf` | Performance improvement |
| `style` | Formatting, lint, no logic change |

## Branch naming (recommended)

```
<type>/<topic>-<issue-id>
```

Examples:

- `feat/cards-42`
- `fix/auth-jwt-15`
- `chore/ci-quality`

## Rules

- **Subject line:** `type(topic): description ISSUE-ID` — max ~72 chars where possible.
- **Imperative mood:** "add endpoint", "fix validation" — not "added" or "fixes".
- **Issue ID:** At the **end** of the subject (`#42` or project ticket code).
- **Body:** Explain *why*, not *what* the diff already shows.
- **No PR number** in the commit message — GitHub adds `(#1234)` on merge.

## Examples

```
feat(cards): add hand deal endpoint #12

Players need a server-side deal action for the card game flow.

Changes:
- Add HandDealingService and POST /api/hands/deal
- Add functional tests for 200, 401, 422
```

```
fix(auth): reject expired refresh tokens #8

Refresh tokens past TTL were accepted, allowing stale sessions.

Changes:
- Validate exp claim in refresh JWT handler
- Add regression functional test
```

```
ci(quality): run make lint in GitHub Actions

Align CI with local quality gates from README.

Changes:
- Add test, stan, cs steps to quality workflow
```

## GitHub MCP usage

When calling `push_files` or `create_or_update_file`, set the `message` argument to the filled-in template above.
