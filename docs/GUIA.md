# Guia do Axe Fork

## Publicação

Crie posts em `content/` usando Markdown e front matter. Campos disponíveis: `title`, `date`, `status`, `publish_at`, `tags`, `categories`, `series`, `author`, `cover` e `description`.

`status: draft` não publica o post. `publish_at` agenda a publicação conforme o horário do servidor. A capa deve apontar para um arquivo em `content/assets/`, por exemplo `assets/capa.jpg`; opcionalmente execute `./scripts/optimize-assets.sh` para gerar WebP.

Use `./scripts/new-post.sh "Título"`, `./scripts/build.sh`, `./scripts/validate.sh` e `./scripts/serve.sh` para criar, gerar, validar e pré-visualizar o site.

## Saída e segurança

O build limpa `public/` antes de gerar novamente, preservando `.htaccess` e `.gitkeep`. Sirva somente essa pasta. O Markdown é escapado: HTML inserido no conteúdo não é executado. O diretório `axe/` contém o legado e não participa do build seguro.

## Recursos gerados

O site produz índice paginado, páginas de tag/categoria/série/autor, busca estática, RSS geral e por tag, sitemap, página 404, tema claro/escuro e assets locais em `public/assets/`.
