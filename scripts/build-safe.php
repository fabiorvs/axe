#!/usr/bin/env php
<?php
declare(strict_types=1);

/** Gerador estático seguro: Markdown entra, HTML estático sai. */
$root = dirname(__DIR__);
$config = $root . '/axe/axe_config.php';
if (!is_file($config)) { fwrite(STDERR, "Configuração ausente: axe/axe_config.php\n"); exit(1); }
require $config;

$contentDir = $root . '/content';
$outputDir = $root . '/public';
$theme = rtrim((string)($blogparms['THEME'] ?? 'lumen/'), '/') . '/';
$themeDir = $root . '/axethemes/' . $theme;
if (!is_dir($contentDir) || !is_dir($themeDir)) { fwrite(STDERR, "Conteúdo ou tema não encontrado.\n"); exit(1); }

function esc(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function slug(string $value): string {
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
    $value = strtolower((string)preg_replace('/[^a-zA-Z0-9]+/', '-', $value));
    return trim($value, '-') ?: 'post';
}
function markdown(string $text): string {
    $lines = preg_split('/\R/', trim($text)); $html = []; $paragraph = []; $quote = [];
    $flushParagraph = function () use (&$paragraph, &$html): void {
        if ($paragraph) { $html[] = '<p>' . inline(implode(' ', $paragraph)) . '</p>'; $paragraph = []; }
    };
    $flushQuote = function () use (&$quote, &$html): void {
        if ($quote) { $html[] = '<blockquote><p>' . inline(implode(' ', $quote)) . '</p></blockquote>'; $quote = []; }
    };
    foreach ($lines as $line) {
        if ($line === '') { $flushParagraph(); $flushQuote(); continue; }
        if (preg_match('/^>\s?(.*)$/', $line, $m)) { $flushParagraph(); $quote[] = $m[1]; continue; }
        $flushQuote();
        if (preg_match('/^(#{1,3})\s+(.+)$/', $line, $m)) { $flushParagraph(); $level = strlen($m[1]) + 1; $html[] = "<h$level>" . inline($m[2]) . "</h$level>"; continue; }
        $paragraph[] = $line;
    }
    $flushParagraph(); $flushQuote();
    return implode("\n", $html);
}
function inline(string $text): string {
    $text = esc($text);
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);
    $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $text);
    return (string)preg_replace_callback('/\[([^]]+)\]\((https?:\/\/[^\s)]+)\)/', static fn($m) => '<a href="' . esc($m[2]) . '" rel="noopener noreferrer">' . $m[1] . '</a>', $text);
}
function parsePost(string $file): array {
    $raw = (string)file_get_contents($file); $meta = [];
    if (preg_match('/^---\R(.*?)\R---\R?(.*)$/s', $raw, $m)) {
        foreach (preg_split('/\R/', $m[1]) as $line) { if (str_contains($line, ':')) { [$key, $value] = explode(':', $line, 2); $meta[trim($key)] = trim($value); } }
        $raw = $m[2];
    }
    $title = $meta['title'] ?? basename($file, '.md');
    $date = $meta['date'] ?? date('Y-m-d');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) throw new RuntimeException("Data inválida em $file");
    return ['title' => $title, 'slug' => slug($meta['slug'] ?? $title), 'date' => $date, 'status' => strtolower($meta['status'] ?? 'published'), 'publish_at' => $meta['publish_at'] ?? $date . ' 00:00:00', 'tags' => array_values(array_filter(array_map('trim', explode(',', $meta['tags'] ?? '')))), 'categories' => array_values(array_filter(array_map('trim', explode(',', $meta['categories'] ?? '')))), 'series' => array_values(array_filter(array_map('trim', explode(',', $meta['series'] ?? '')))), 'author' => trim($meta['author'] ?? ''), 'cover' => trim($meta['cover'] ?? ''), 'description' => $meta['description'] ?? '', 'body' => markdown($raw)];
}
function render(string $template, array $values): string { return strtr($template, array_combine(array_map(static fn($k) => '%%' . $k . '%%', array_keys($values)), array_values($values))); }
function save(string $file, string $contents): void { $dir = dirname($file); if (!is_dir($dir)) mkdir($dir, 0755, true); file_put_contents($file, $contents, LOCK_EX); }
function copyTree(string $source, string $destination): void {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($iterator as $item) {
        $target = $destination . '/' . $iterator->getSubPathName();
        if ($item->isDir()) { if (!is_dir($target)) mkdir($target, 0755, true); }
        else { if (!is_dir(dirname($target))) mkdir(dirname($target), 0755, true); copy($item->getPathname(), $target); }
    }
}
function cleanOutput(string $dir): void {
    foreach (glob($dir . '/*') ?: [] as $path) { if (in_array(basename($path), ['.htaccess', '.gitkeep'], true)) continue; if (is_dir($path) && !is_link($path)) { $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST); foreach ($it as $item) $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname()); rmdir($path); } else unlink($path); }
}

$settings = [
    'BLOGTITLE' => esc((string)($blogparms['BLOGTITLE'] ?? 'Axe')), 'BLOGMOTTO' => esc((string)($blogparms['BLOGMOTTO'] ?? '')),
    'BLOGURL' => rtrim((string)($blogparms['BLOGURL'] ?? '/'), '/') . '/', 'FEEDURL' => esc((string)($blogparms['FEEDURL'] ?? 'feed.xml')),
    'BLOGOWNER' => esc((string)($blogparms['BLOGOWNER'] ?? '')), 'YEAR' => date('Y'), 'AXEVERSION' => 'Axe Safe Build',
    'THEMESPATH' => esc((string)($blogparms['THEMESPATH'] ?? 'axethemes/')), 'THEME' => esc($theme), 'NAVLINKS' => '', 'PAGEURL' => '', 'PAGETITLE' => '', 'PAGEDESC' => '',
];
cleanOutput($outputDir);
$posts = array_values(array_filter(array_map('parsePost', glob($contentDir . '/*.md') ?: []), static fn($post) => $post['status'] === 'published' && strtotime($post['publish_at']) <= time()));
foreach ($posts as &$post) $post['url'] = 'posts/' . $post['date'] . '-' . $post['slug'] . '.html';
unset($post);
usort($posts, static fn($a, $b) => strcmp($b['date'], $a['date']));
if (!$posts) { fwrite(STDERR, "Nenhum post Markdown encontrado em content/.\n"); exit(1); }

$header = (string)file_get_contents($themeDir . 'header.php'); $footer = (string)file_get_contents($themeDir . 'footer.php');
$card = (string)file_get_contents($themeDir . 'capa-post.php'); $single = (string)file_get_contents($themeDir . 'single-body.php');
$baseUrl = $settings['BLOGURL']; $entries = []; $tagMap = []; $taxonomies = ['category' => [], 'series' => [], 'author' => []];
foreach ($posts as $post) {
    $url = $post['url'];
    foreach ($post['tags'] as $tag) $tagMap[$tag][] = $post;
    foreach ($post['categories'] as $term) $taxonomies['category'][$term][] = $post;
    foreach ($post['series'] as $term) $taxonomies['series'][$term][] = $post;
    if ($post['author']) $taxonomies['author'][$post['author']][] = $post;
    $tagLinks = implode(', ', array_map(static fn($tag) => '<a href="tag-' . slug($tag) . '.html">' . esc($tag) . '</a>', $post['tags']));
    $cover = $post['cover'] ? '<figure class="post-cover"><picture><source srcset="' . esc($post['cover']) . '.webp" type="image/webp"><img src="' . esc($post['cover']) . '" alt="' . esc($post['title']) . '" loading="lazy"></picture></figure>' : '';
    $values = array_replace($settings, ['POSTTITLE' => esc($post['title']), 'POSTDATE' => date('d/m/Y', strtotime($post['date'])), 'POSTBODY' => $post['body'], 'POSTNEWSBODY' => $post['body'], 'POSTTAGS' => $tagLinks, 'POSTCOVER' => $cover, 'POSTURL' => $url, 'POSTSURLPREFIX' => '', 'PAGEURL' => $baseUrl . $url, 'PAGEDESC' => esc($post['description']), 'PAGETITLE' => esc($post['title'] . ' · ' . $blogparms['BLOGTITLE'])]);
    save($outputDir . '/' . $url, render($header, $values) . render($single, $values) . render($footer, $values));
    $entries[] = render($card, $values);
}
$indexValues = array_replace($settings, ['PAGETITLE' => $settings['BLOGTITLE'], 'PAGEDESC' => $settings['BLOGMOTTO'], 'PAGEURL' => $baseUrl, 'POSTSURLPREFIX' => '', 'POSTTITLE' => '', 'POSTDATE' => '', 'POSTBODY' => '', 'POSTTAGS' => '', 'POSTURL' => '']);
$pageSize = max(1, (int)($blogparms['POSTS_PER_PAGE'] ?? 10)); $pages = array_chunk($entries, $pageSize);
foreach ($pages as $number => $pageEntries) { $n = $number + 1; $nav = ''; if ($n > 1) $nav .= '<a class="prev" href="' . ($n === 2 ? 'index.html' : 'page/' . ($n - 1) . '.html') . '">← Mais recentes</a>'; if ($n < count($pages)) $nav .= '<a class="next" href="page/' . ($n + 1) . '.html">Mais antigos →</a>'; $v = $indexValues; $v['NAVLINKS'] = $nav; $v['PAGEURL'] = $baseUrl . ($n === 1 ? '' : 'page/' . $n . '.html'); save($outputDir . '/' . ($n === 1 ? 'index.html' : 'page/' . $n . '.html'), render($header, $v) . implode("\n", $pageEntries) . render($footer, $v)); }
foreach ($tagMap as $tag => $tagPosts) {
    $body = '<header class="tag-header"><p class="eyebrow">TAG</p><h1>' . esc($tag) . '</h1></header>';
    foreach ($tagPosts as $post) $body .= '<article class="archive-item"><time>' . esc($post['date']) . '</time><h2><a href="' . esc($post['url']) . '">' . esc($post['title']) . '</a></h2></article>';
    $v = $indexValues; $v['PAGETITLE'] = 'Tag: ' . esc($tag); $v['PAGEURL'] = $baseUrl . 'tag-' . slug($tag) . '.html'; save($outputDir . '/tag-' . slug($tag) . '.html', render($header, $v) . $body . render($footer, $v));
}
$archive = static function(string $kind, string $term, array $items) use ($header, $footer, $indexValues, $outputDir, $baseUrl): void { $body = '<header class="tag-header"><p class="eyebrow">' . strtoupper($kind) . '</p><h1>' . esc($term) . '</h1></header>'; foreach ($items as $post) $body .= '<article class="archive-item"><time>' . esc($post['date']) . '</time><h2><a href="' . esc($post['url']) . '">' . esc($post['title']) . '</a></h2></article>'; $v = $indexValues; $v['PAGETITLE'] = esc($term); $v['PAGEURL'] = $baseUrl . $kind . '-' . slug($term) . '.html'; save($outputDir . '/' . $kind . '-' . slug($term) . '.html', render($header, $v) . $body . render($footer, $v)); };
foreach ($taxonomies as $kind => $terms) foreach ($terms as $term => $items) $archive($kind, $term, $items);
$rss = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/"><channel><title>' . $settings['BLOGTITLE'] . '</title><link>' . esc($baseUrl) . '</link><description>' . $settings['BLOGMOTTO'] . '</description><language>pt-BR</language>';
foreach ($posts as $post) $rss .= '<item><title>' . esc($post['title']) . '</title><link>' . esc($baseUrl . $post['url']) . '</link><pubDate>' . date(DATE_RSS, strtotime($post['date'])) . '</pubDate><description>' . esc(strip_tags($post['body'])) . '</description><content:encoded><![CDATA[' . $post['body'] . ']]></content:encoded></item>';
save($outputDir . '/feed.xml', $rss . '</channel></rss>');
foreach ($tagMap as $tag => $items) { $feed = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel><title>' . $settings['BLOGTITLE'] . ' · ' . esc($tag) . '</title>'; foreach ($items as $post) $feed .= '<item><title>' . esc($post['title']) . '</title><link>' . esc($baseUrl . $post['url']) . '</link></item>'; save($outputDir . '/feed-tag-' . slug($tag) . '.xml', $feed . '</channel></rss>'); }
$urls = array_merge([$baseUrl], array_map(static fn($post) => $baseUrl . $post['url'], $posts)); save($outputDir . '/sitemap.xml', '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . implode('', array_map(static fn($url) => '<url><loc>' . esc($url) . '</loc></url>', $urls)) . '</urlset>');
$search = array_map(static fn($post) => ['title' => $post['title'], 'description' => $post['description'], 'url' => $post['url'], 'search' => strtolower($post['title'] . ' ' . $post['description'] . ' ' . implode(' ', $post['tags']))], $posts); save($outputDir . '/search.json', json_encode($search, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
copyTree($themeDir, $outputDir . '/axethemes/' . rtrim($theme, '/'));
if (is_dir($contentDir . '/assets')) copyTree($contentDir . '/assets', $outputDir . '/assets');
save($outputDir . '/404.html', render($header, array_replace($indexValues, ['PAGETITLE' => 'Página não encontrada', 'PAGEDESC' => 'Erro 404'])) . '<article class="post-card"><p class="eyebrow">404</p><h1>Página não encontrada</h1><p>Volte para a <a href="' . $baseUrl . '">página inicial</a>.</p></article>' . render($footer, $indexValues));
echo "Build seguro concluído: " . count($posts) . " post(s).\n";
