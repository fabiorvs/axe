#!/usr/bin/env bash
set -euo pipefail
title=${1:?Uso: ./scripts/new-post.sh "Título do post"}
root=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
slug=$(printf '%s' "$title" | iconv -t ascii//TRANSLIT 2>/dev/null | tr '[:upper:]' '[:lower:]' | sed -E 's/[^a-z0-9]+/-/g;s/^-|-$//g')
file="$root/content/$(date +%Y-%m-%d)-${slug}.md"
test ! -e "$file" || { echo "Post já existe: $file" >&2; exit 1; }
printf -- '---\ntitle: %s\ndate: %s\nstatus: draft\ntags: \ncategories: \nseries: \nauthor: \ncover: \n---\n\nEscreva seu post aqui.\n' "$title" "$(date +%F)" > "$file"
echo "Criado: $file"
