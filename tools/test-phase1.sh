#!/usr/bin/env bash
set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$repo_dir"

find wordpress tests/php -type f -name '*.php' -print0 | xargs -0 -n1 php -l
php tests/php/run.php

while IFS= read -r required_copy; do
    if [[ -n "$required_copy" ]] && ! grep -R -F -q "$required_copy" wordpress; then
        echo "Missing required copy: $required_copy" >&2
        exit 1
    fi
done < tests/fixtures/phase1-required-copy.txt

if grep -R -E -n 'https?://(fonts|cdn|api)\.' wordpress --include='*.php' --include='*.css' --include='*.js'; then
    echo 'Unexpected external production dependency detected.' >&2
    exit 1
fi

echo 'Phase 1 validation passed.'
