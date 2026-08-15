#!/usr/bin/env bash
set -euo pipefail
repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$repo_dir"
find wordpress -name '*.php' -print0 | xargs -0 -n1 php -l >/dev/null
php tests/php/phase6.php
