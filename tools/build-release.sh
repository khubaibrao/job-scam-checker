#!/usr/bin/env bash
set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
release_dir="$repo_dir/release"
stage_dir="$(mktemp -d)"
trap 'rm -rf "$stage_dir"' EXIT

version="1.0.0"
plugin_name="job-scam-checker-$version.zip"
theme_name="job-scam-checker-theme-$version.zip"

mkdir -p "$release_dir" "$stage_dir/plugin" "$stage_dir/theme"
cp -R "$repo_dir/wordpress/plugins/job-scam-checker" "$stage_dir/plugin/"
cp -R "$repo_dir/wordpress/themes/job-scam-checker-theme" "$stage_dir/theme/"

find "$stage_dir" -type f \( -name '.DS_Store' -o -name 'Thumbs.db' -o -name '*.log' \) -delete
rm -f "$release_dir/$plugin_name" "$release_dir/$theme_name" "$release_dir/SHA256SUMS"

( cd "$stage_dir/plugin" && zip -q -X -D -r "$release_dir/$plugin_name" job-scam-checker )
( cd "$stage_dir/theme" && zip -q -X -D -r "$release_dir/$theme_name" job-scam-checker-theme )
( cd "$release_dir" && sha256sum "$plugin_name" "$theme_name" > SHA256SUMS )

echo "Built release/$plugin_name"
echo "Built release/$theme_name"
cat "$release_dir/SHA256SUMS"
