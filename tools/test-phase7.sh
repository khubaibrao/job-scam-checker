#!/usr/bin/env bash
set -euo pipefail
repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$repo_dir"
php -l tests/php/phase7.php >/dev/null
php tests/php/phase7.php
echo 'Phase 7 validation passed.'
