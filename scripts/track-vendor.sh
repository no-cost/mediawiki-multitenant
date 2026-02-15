#!/usr/bin/env bash
# force-tracks vendor/ dirs inside extensions that have their own .gitignore excluding them
# run after adding or updating extensions; also called by the pre-commit hook

set -euo pipefail

cd "$(git -C "$(dirname "$0")" rev-parse --show-toplevel)"

git add -f extensions/*/vendor/ 2>/dev/null || true
