#!/usr/bin/env php
<?php
declare(strict_types=1);

/** Converte um descriptor PHP estático legado em Markdown sem executá-lo. */
if ($argc !== 3) { fwrite(STDERR, "Uso: php scripts/migrate-legacy-post.php origem.php destino.md\n"); exit(1); }
$raw = (string)file_get_contents($argv[1]);
function legacyField(string $raw, string $field): string {
    $pattern = '/\\$POST\[[\'\"]' . preg_quote($field, '/') . '[\'\"]\]\s*=\s*[\'\"]((?:\\\\.|[^\'\"])*)[\'\"]\s*;/s';
    return preg_match($pattern, $raw, $match) ? stripcslashes($match[1]) : '';
}
$title = legacyField($raw, 'POSTTITLE');
$tags = legacyField($raw, 'POSTTAGS');
$date = legacyField($raw, 'POSTTIME');
if (!preg_match('/\\$POST\["POSTBODY"\]\s*=\s*<<<\s*(\w+)\R(.*?)\R\1;/s', $raw, $match)) { fwrite(STDERR, "Descriptor sem POSTBODY em heredoc: não convertido.\n"); exit(1); }
$day = $date ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
$markdown = "---\ntitle: " . ($title ?: 'Sem título') . "\ntags: $tags\ndate: $day\n---\n\n" . trim($match[2]) . "\n";
file_put_contents($argv[2], $markdown, LOCK_EX);
echo "Convertido sem executar PHP: {$argv[2]}\n";
