#!/usr/bin/env bash
# Smoke tests for games-cards API. Requires app running (make up).
set -euo pipefail

BASE_URL="${1:-http://localhost:8080}"
API_USER="${API_USER:-api_user}"
API_PASSWORD="${API_PASSWORD:-demo}"

echo "Smoke tests against ${BASE_URL}"

curl -sf "${BASE_URL}/demo" >/dev/null
echo "  OK  GET /demo"

curl -sf "${BASE_URL}/api/doc.json" >/dev/null
echo "  OK  GET /api/doc.json"

TOKEN="$(
  curl -s -X POST "${BASE_URL}/api/login_check" \
    -H 'Content-Type: application/json' \
    -d "{\"username\":\"${API_USER}\",\"password\":\"${API_PASSWORD}\"}" \
  | php -r 'echo json_decode(file_get_contents("php://stdin"), true)["token"] ?? "";'
)"

if [ -z "${TOKEN}" ]; then
  echo "  FAIL login — no token"
  exit 1
fi
echo "  OK  POST /api/login_check"

curl -sf -H "Authorization: Bearer ${TOKEN}" "${BASE_URL}/cards" >/dev/null
echo "  OK  GET /cards (authenticated)"

curl -sf -X POST \
  -H "Authorization: Bearer ${TOKEN}" \
  -H 'Content-Type: application/json' \
  -d '{"count":5}' \
  "${BASE_URL}/api/hands/deal" >/dev/null
echo "  OK  POST /api/hands/deal"

HTTP_CODE="$(curl -s -o /dev/null -w '%{http_code}' -X POST \
  -H 'Content-Type: application/json' \
  -d '{"count":5}' \
  "${BASE_URL}/api/hands/deal")"
if [ "${HTTP_CODE}" != "401" ]; then
  echo "  FAIL unauthenticated deal — expected 401, got ${HTTP_CODE}"
  exit 1
fi
echo "  OK  POST /api/hands/deal without token → 401"

echo "All smoke tests passed."
