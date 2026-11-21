# Guia completo do Axe Fork

O Axe Fork gera blogs estáticos: lê Markdown em `content/` e publica HTML em `public/`, sem executar código escrito nos posts.

## Requisitos e configuração

São necessários PHP 8.1+, Bash e, opcionalmente, `cwebp` para WebP. Crie a configuração local:

```bash
cp axe/axe_config_exemplo.php axe/axe_config.php
```

Defina título, URL, autor, diretórios e tema. No preview, `public/` já é a raiz HTTP:

```php
$blogparms['BLOGURL'] = 'http://localhost:8080/';
$blogparms['FEEDURL'] = 'http://localhost:8080/feed.xml';
```

`axe_config.php` é local e não deve ser versionado.

## Posts

Crie um rascunho com `./scripts/new-post.sh "Título"`. Posts ficam em `content/`:

```markdown
---
title: Artigo de exemplo
date: 2026-11-21
status: published
publish_at: 2026-11-21 09:00:00
tags: axe, php
categories: Desenvolvimento
series: Guia do Axe
author: Equipe Axe
cover: assets/capa.jpg
description: Resumo para busca e SEO.
---

Texto em **Markdown**.
```

`draft` não publica; `publish_at` agenda posts futuros. Tags geram páginas e feeds próprios; categorias, séries e autores geram arquivos. HTML no conteúdo é escapado por segurança.

## Imagens

Coloque assets em `content/assets/`; o build copia tudo para `public/assets/`. Para capas, use `cover: assets/capa.jpg`. Gere WebP com:

```bash
./scripts/optimize-assets.sh
```

Quando houver `assets/capa.webp`, o tema a usa como fonte preferencial.

## Comandos

| Comando | Finalidade |
| --- | --- |
| `./scripts/build.sh` | Limpa e gera `public/`. |
| `./scripts/validate.sh` | Valida sintaxe e saída essencial. |
| `./tests/safe-build.sh` | Testa escape de HTML/script em conteúdo. |
| `./scripts/serve.sh` | Gera e abre preview em `http://localhost:8080/`. |

O build remove a saída antiga, preservando `.htaccess` e `.gitkeep`. Não mantenha arquivos manuais em `public/`.

## Saída e deploy

São gerados índice paginado, posts, arquivos de tag/categoria/série/autor, `feed.xml`, feeds por tag, `search.json`, `sitemap.xml`, `404.html`, assets e tema. Ajuste `POSTS_PER_PAGE` para definir a paginação.

Publique apenas `public/`. Nunca exponha `axe/`, `content/`, `axethemes/`, scripts, testes ou `axe_config.php`. Com Apache/Nginx, use `public/` como document root.

## Migração do legado

O núcleo antigo está preservado para referência, mas não participa do build. Converta descriptors estáticos sem executá-los:

```bash
php scripts/migrate-legacy-post.php origem.php content/destino.md
```

Revise sempre o Markdown convertido.

## Problemas comuns

- **CSS abre o índice:** use `http://localhost:8080/`, não `/public/`; confira `BLOGURL`.
- **Busca indisponível:** confirme que `public/search.json` foi gerado e que o servidor não responde HTML para esse arquivo.
- **Post ausente:** confira `status`, `date`, `publish_at` e rode o build.
- **Capa ausente:** confirme o arquivo em `content/assets/` e rode o build novamente.
