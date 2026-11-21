#!/usr/bin/env bash
set -euo pipefail
root=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
"$root/scripts/build.sh"
echo "Servindo em http://localhost:8080/"
php -S localhost:8080 -t "$root/public"
