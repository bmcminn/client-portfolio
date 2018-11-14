<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <title>Loading...</title>
    <link rel="stylesheet" type="text/css" href="/resources/css/modern-normalize.css">
    <link rel="stylesheet" type="text/css" href="/resources/css/main.css">

    <script src="/resources/js/libs/modernizr.min.js"></script>
</head>
<body>
    <div id="app">
        <router-view></router-view>
    </div>

<?php
// RENDER TEMPLATE FILES
$partials = glob(__DIR__.'/partials/*.html');

foreach ($partials as $partial) {
    $content = file_get_contents($partial);
    $basename = basename($partial, '.html');
    echo "<template id=\"{$basename}\">{$content}</template>" . PHP_EOL;
}
?>


<?php

// PROCESS ENV BUILD RESOURCES

$ENV = $argv[1] ?? 'local';

// PROD FILES
$scripts = [
    [
        'file'  => '',
        'int'   => '',
    ]
    [
        'file' => 'https://cdn.jsdelivr.net/npm/vuex-persist'
    ]
];

if ($ENV === 'local') {
    $scripts = [
        [
            'file'  => '',
            'int'   => '',
        ]
        [
            'file' => 'https://cdn.jsdelivr.net/npm/vuex-persist'
        ]
    ];
}


foreach ($scripts as $script) {
    $file   = $script['file'];
    $int    = $script['int'] ?? null;

    if ($int) {
        echo "<script src=\"{$file}\" integrity=\"{$int}\" source=\"anonymous\"></script>" . PHP_EOL;
    } else {
        echo "<script src=\"{$file}\"></script>" . PHP_EOL;
    }

}


?>
    <script src="/resources/js/libs/axios.min.js"></script>
    <script src="/resources/js/libs/lodash.min.js"></script>
    <script src="/resources/js/libs/no-touchy.js"></script>
    <!-- <script src="/resources/js/libs/vue.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.js"></script>
    <script src="/resources/js/libs/vue-router.min.js"></script>
    <script src="/resources/js/libs/vuex.min.js"></script>
    <script src="/resources/js/libs/vuex-persist.js"></script>


    https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js" integrity="sha256-mpnrJ5DpEZZkwkE1ZgkEQQJW/46CSEh/STrZKOB/qoM="
    https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js" integrity="sha256-FtWfRI+thWlNz2sB3SJbwKx5PgMyKIVgwHCTwa3biXc="
    https://cdnjs.cloudflare.com/ajax/libs/vue-router/3.0.1/vue-router.min.js" integrity="sha256-yEB9jUlD51i5kxJZlzgzfR6XmVKI76Nl1WRA1aqIilU="
    https://cdnjs.cloudflare.com/ajax/libs/vuex/3.0.1/vuex.min.js" integrity="sha256-1QlN0ckC4jlz91DZixPZxTv9vYpcBmS7sK7HA8xFmFA="
    https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.min.js


    <script src="/resources/js/main.js"></script>
</body>
</html>
