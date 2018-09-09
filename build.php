#!/usr/bin/php
<?php

$phpfiles = array(
    'GUI.php',
    'vendor/splitbrain/php-archive/src/ArchiveCorruptedException.php',
    'vendor/splitbrain/php-archive/src/ArchiveIllegalCompressionException.php',
    'vendor/splitbrain/php-archive/src/ArchiveIOException.php',
    'vendor/splitbrain/php-archive/src/FileInfoException.php',
    'vendor/splitbrain/php-archive/src/Archive.php',
    'vendor/splitbrain/php-archive/src/FileInfo.php',
    'vendor/splitbrain/php-archive/src/Tar.php',
    'HTTPClient.php',
);

$htmls = glob('res/*.html');
$styles = glob('res/*.css');
sort($styles);
$scripts = glob('res/*.js');
sort($scripts);

// header
$source = "<?php\n";
$source .= "/*> __NOPHP__ <!--*/\n";
$source .= "// built " . date('Y-m-d H:I:s') . "\n";
$source .= "define('ISBUILD', true);\n";

foreach ($phpfiles as $file) {
    $content = file_get_contents($file);
    $content = preg_replace('/^<\?php/', '', $content);
    $content = preg_replace('/^namespace .*$/m', '', $content);
    $content = preg_replace('/^use .*$/m', '', $content);
    $source .= $content;
}

foreach ($htmls as $file) {
    $name = '__' . strtoupper(basename($file, '.html')) . '__';
    $html = file_get_contents($file);
    $html = addcslashes($html, "'");
    $source = str_replace($name, $html, $source);
}

$css = '';
foreach ($styles as $file) {
    $css .= addcslashes(file_get_contents($file), "'");
}
$source = str_replace('__STYLES__', $css, $source);

$js = '';
foreach ($scripts as $file) {
    $js .= addcslashes(file_get_contents($file), "'");
}
$source = str_replace('__SCRIPTS__', $js, $source);



// main
$source .= "\n\n";
$source .= "\$gui = new GUI();\n";
$source .= "\$gui->run();\n";
$source .= "//-->\n";

if (!is_dir('dist')) mkdir('dist');
copy('README.md', 'dist/README.md');
file_put_contents('dist/dokuwiki-downloader.php', $source);
$ok = 0;
system('php -l dist/dokuwiki-downloader.php', $ok);

exit($ok);






