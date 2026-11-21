#!/usr/bin/env bash
set -euo pipefail
project_dir=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
fixture="$project_dir/content/__security-test.md"
trap 'rm -f "$fixture"; "$project_dir/scripts/build.sh" >/dev/null' EXIT
cat > "$fixture" <<'EOF'
---
title: Conteúdo inseguro
date: 2026-11-21
---

<script>window.bad = true</script>
EOF
"$project_dir/scripts/build.sh" >/dev/null
if rg -q '<script>window.bad = true</script>' "$project_dir/public/posts/2026-11-21-conteudo-inseguro.html"; then
  echo "Falha: HTML de conteúdo não foi escapado." >&2
  exit 1
fi
rg -q '&lt;script&gt;window.bad = true&lt;/script&gt;' "$project_dir/public/posts/2026-11-21-conteudo-inseguro.html"
echo "Teste de segurança do conteúdo aprovado."
