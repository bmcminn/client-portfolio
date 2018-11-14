<?php

$ENV = strtolower($argv[1] ?? 'local');
define('ENV', $ENV);



function renderStyles(array $styles) {
    global $ENV;

    foreach ($styles as $style) {
        $file = $style[$ENV];
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$file}\">" . PHP_EOL;
    }
}



function renderPartials() {
    $partials = glob(__DIR__.'/partials/*.html');

    foreach ($partials as $partial) {
        $content = file_get_contents($partial);
        $basename = basename($partial, '.html');
        echo "<template id=\"{$basename}\">{$content}</template>" . PHP_EOL;
    }
}



function renderScript(array $script) {
    global $ENV;

    $file   = $script[$ENV];
    $int    = $script['int'] ?? null;

    if ($int && $ENV === 'prod') {
        echo "<script src=\"{$file}\" integrity=\"{$int}\" source=\"anonymous\"></script>" . PHP_EOL;
    } else {
        echo "<script src=\"{$file}\"></script>" . PHP_EOL;
    }
}



function renderScripts(array $scripts) {
    foreach ($scripts as $script) {
        renderScript($script);
    }
}
