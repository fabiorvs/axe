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
    document.addEventListener('DOMContentLoaded', function () {
      var toggle = document.querySelector('.theme-toggle');
      if (localStorage.getItem('axe-theme') === 'dark') document.documentElement.dataset.theme = 'dark';
      if (toggle) toggle.addEventListener('click', function () { var dark = document.documentElement.dataset.theme !== 'dark'; document.documentElement.dataset.theme = dark ? 'dark' : ''; localStorage.setItem('axe-theme', dark ? 'dark' : 'light'); });
      var search = document.getElementById('site-search'), results = document.getElementById('search-results');
      if (search) fetch(document.querySelector('.brand').href + 'search.json').then(function (r) { return r.json(); }).then(function (posts) { search.addEventListener('input', function () { var q = search.value.toLowerCase().trim(); results.innerHTML = q ? posts.filter(function (p) { return p.search.indexOf(q) !== -1; }).slice(0, 8).map(function (p) { return '<a class="search-result" href="' + p.url + '"><strong>' + p.title + '</strong><br><small>' + p.description + '</small></a>'; }).join('') : ''; }); });
    });
  </script>
</head>
<body>
  <div class="site-shell">
    <header class="site-header">
      <a class="brand" href="%%BLOGURL%%"><span class="brand-mark">L</span><span>%%BLOGTITLE%%</span></a>
      <div class="header-tools"><button class="theme-toggle" type="button" aria-label="Alternar tema">◐</button><a class="feed-link" href="%%FEEDURL%%">RSS <span aria-hidden="true">↗</span></a></div>
    </header>
    <div class="search-wrap"><label for="site-search">Buscar</label><input id="site-search" type="search" placeholder="Título, tag ou categoria"><div id="search-results"></div></div>
    <main class="layout">
      <section class="content" aria-label="Publicações">
