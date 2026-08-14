#!/usr/bin/env bash
set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$repo_dir"

bash tools/test-phase1.sh
bash tools/test-phase2.sh
bash tools/test-phase3.sh

echo 'All Phase 1, Phase 2, and Phase 3 tests passed.'
