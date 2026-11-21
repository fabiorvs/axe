#!/usr/bin/env bash
set -euo pipefail
root=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
assets="$root/content/assets"
test -d "$assets" || { echo "Crie content/assets primeiro."; exit 0; }
command -v cwebp >/dev/null || { echo "cwebp não instalado; assets originais serão copiados pelo build."; exit 0; }
find "$assets" -type f \( -iname '*.jpg' -o -iname '*.jpeg' -o -iname '*.png' \) -print0 | while IFS= read -r -d '' image; do cwebp -quiet -q 82 "$image" -o "${image%.*}.webp"; done
echo "Imagens WebP geradas em content/assets."
