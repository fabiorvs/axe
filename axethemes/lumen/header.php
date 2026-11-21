<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="%%PAGEDESC%%">
  <meta property="og:title" content="%%PAGETITLE%%">
  <meta property="og:description" content="%%PAGEDESC%%">
  <meta property="og:url" content="%%PAGEURL%%">
  <title>%%PAGETITLE%%</title>
  <link rel="stylesheet" href="%%BLOGURL%%%%THEMESPATH%%%%THEME%%css/style.css">
  <link rel="alternate" type="application/rss+xml" title="%%BLOGTITLE%%" href="%%FEEDURL%%">
  <script defer>
    document.addEventListener('click', function (event) {
      var button = event.target.closest('[data-copy-link]');
      if (!button || !navigator.clipboard) return;
      navigator.clipboard.writeText(button.dataset.copyLink).then(function () {
        button.textContent = 'Link copiado';
        window.setTimeout(function () { button.textContent = 'Copiar link'; }, 1800);
      });
    });
  </script>
</head>
<body>
  <div class="site-shell">
    <header class="site-header">
      <a class="brand" href="%%BLOGURL%%"><span class="brand-mark">L</span><span>%%BLOGTITLE%%</span></a>
      <a class="feed-link" href="%%FEEDURL%%">RSS <span aria-hidden="true">↗</span></a>
    </header>
    <main class="layout">
      <section class="content" aria-label="Publicações">
