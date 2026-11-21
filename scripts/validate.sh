#!/usr/bin/env bash
set -euo pipefail

project_dir=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)

find "$project_dir/axe" "$project_dir/axethemes" -name '*.php' -print0 | xargs -0 -n1 php -l >/dev/null
for file in public/index.html public/feed.xml public/sitemap.xml; do
  test -s "$project_dir/$file"
done

theme=$(cd "$project_dir/axe" && php -r 'require "axe_config.php"; echo $blogparms["THEME"];')
test -s "$project_dir/public/axethemes/$theme/css/style.css"
echo "Validação concluída: PHP, páginas essenciais e assets estão presentes."
