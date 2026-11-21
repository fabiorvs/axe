#!/usr/bin/env bash
set -euo pipefail

project_dir=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
config_file="$project_dir/axe/axe_config.php"

if [[ ! -f "$config_file" ]]; then
  echo "Configuração ausente: copie axe/axe_config_exemplo.php para axe/axe_config.php." >&2
  exit 1
fi

php "$project_dir/scripts/build-safe.php"
