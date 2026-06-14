#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo ">> Fresh git init for games-cards"
rm -rf .git
git init -b main
git add .
git commit -m "Initial commit: Symfony 6.4 card dealing demo"
git remote add origin git@github.com:BenArfanour/games-cards.git

if command -v gh >/dev/null 2>&1 && gh auth status >/dev/null 2>&1; then
  echo ">> Creating GitHub repo with gh and pushing..."
  gh repo create BenArfanour/games-cards --public \
    --description "Symfony 6.4 card dealing demo" \
    --source=. --remote=origin --push
else
  echo ">> gh not available — pushing to origin (create empty repo first if needed):"
  echo "   https://github.com/new  → name: games-cards"
  git push -u origin main
fi

echo ">> Done: https://github.com/BenArfanour/games-cards"
git remote -v
git log -1 --oneline
