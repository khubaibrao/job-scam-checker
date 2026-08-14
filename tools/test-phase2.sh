#!/usr/bin/env bash
set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$repo_dir"

php tests/php/phase2.php
node --check wordpress/plugins/job-scam-checker/assets/js/checker.js

if grep -R -E -n '(curl_exec|wp_remote_(get|post)|openai|anthropic|paid api)' wordpress/plugins/job-scam-checker --include='*.php' --include='*.js'; then
    echo 'Unexpected external analysis dependency detected.' >&2
    exit 1
fi

echo 'Phase 2 validation passed.'
