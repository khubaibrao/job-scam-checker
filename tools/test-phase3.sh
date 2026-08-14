#!/usr/bin/env bash
set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$repo_dir"

php tests/php/phase3.php
node --check wordpress/plugins/job-scam-checker/assets/js/checker.js

echo 'Phase 3 validation passed.'
