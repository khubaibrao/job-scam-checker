#!/usr/bin/env bash
set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$repo_dir"

bash tools/build-release.sh
php tests/php/phase8.php

plugin_zip="release/job-scam-checker-1.0.0.zip"
theme_zip="release/job-scam-checker-theme-1.0.0.zip"

unzip -tq "$plugin_zip" >/dev/null
unzip -tq "$theme_zip" >/dev/null
unzip -Z1 "$plugin_zip" | grep -qx 'job-scam-checker/job-scam-checker.php'
unzip -Z1 "$theme_zip" | grep -qx 'job-scam-checker-theme/style.css'

if unzip -Z1 "$plugin_zip" "$theme_zip" 2>/dev/null | grep -Eq '(^|/)(tests|docs|tools|\.git|\.github|\.devcontainer|node_modules|vendor)(/|$)|\.log$|\.zip$'; then
    echo 'FAIL: release archive contains a development-only path'
    exit 1
fi

( cd release && sha256sum -c SHA256SUMS )
echo 'Phase 8 release archive validation passed.'
