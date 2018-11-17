<?php
require 'view-functions.php';


$CSS_PATH   = '/resources/css';
$JS_PATH    = '/resources/js';


$styles = [
    [
        'prod'  => $CSS_PATH . '/modern-normalize.min.css',
        'local' => $CSS_PATH . '/modern-normalize.css',
    ],
    [
        'prod'  => $CSS_PATH . '/main.min.css',
        'local' => $CSS_PATH . '/main.css',
    ]
];


$scripts = [
    [
        'local' => $JS_PATH . '/libs/axios.js',
        'prod'  => 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js',
        'int'   => 'sha256-mpnrJ5DpEZZkwkE1ZgkEQQJW/46CSEh/STrZKOB/qoM=',
    ],
    // [
    //     'local' => $JS_PATH . '/libs/lodash.js',
    //     'prod'  => 'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js',
    //     'int'   => 'sha256-7/yoZS3548fXSRXqc/xYzjsmuW3sFKzuvOCHd06Pmps=',
    // ],
    [
        'local' => $JS_PATH . '/libs/no-touchy.js',
        'prod'  => $JS_PATH . '/libs/no-touchy.js',
    ],
    [
        'local' => $JS_PATH . '/libs/vue.js',
        'prod'  => 'https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js',
        'int'   => 'sha256-FtWfRI+thWlNz2sB3SJbwKx5PgMyKIVgwHCTwa3biXc=',
    ],
    [
        'local' => $JS_PATH . '/libs/vue-router.js',
        'prod'  => 'https://cdnjs.cloudflare.com/ajax/libs/vue-router/3.0.1/vue-router.min.js',
        'int'   => 'sha256-yEB9jUlD51i5kxJZlzgzfR6XmVKI76Nl1WRA1aqIilU=',
    ],
    [
        'local' => $JS_PATH . '/libs/vuex.js',
        'prod'  => 'https://cdnjs.cloudflare.com/ajax/libs/vuex/3.0.1/vuex.min.js',
        'int'   => 'sha256-1QlN0ckC4jlz91DZixPZxTv9vYpcBmS7sK7HA8xFmFA=',
    ],
    // [
    //     'local' => $JS_PATH . '/libs/vuex-persist.js',
    //     'prod'  => 'https://cdn.jsdelivr.net/npm/vuex-persist',
    // ],
    [
        'local' => $JS_PATH . '/main.js',
        'prod'  => $JS_PATH . '/main.min.js',
    ],
];


?><!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <title>Loading...</title>

    <?php renderStyles($styles); ?>

    <?php renderScript([
        'prod'  => 'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js',
        'local' => '/resources/js/libs/modernizr.min.js',
        'int'   => 'sha256-0rguYS0qgS6L4qVzANq4kjxPLtvnp5nn2nB5G1lWRv4=',
    ]); ?>
</head>
<body>
    <div id="app">
        <header class="page-header">
            <div class="brand">Logo Text</div>
            <nav>
                <router-link :to="{name:'home'}">Home</router-link>
                <router-link :to="{name:'about'}">About</router-link>
                <router-link :to="{name:'portfolio'}">Portfolio</router-link>
            </nav>
        </header>

        <transition name="fade">
            <router-view></router-view>
        </transition>


        <footer class="page-footer">
            <nav>
                <router-link :to="{name:'privacy'}">Privacy Policy</router-link>
                <router-link :to="{name:'terms'}">Terms of Use</router-link>
                <router-link :to="{name:'login'}">Client Login</router-link>
            </nav>
        </footer>
    </div>

    <?php renderPartials(); ?>
    <?php renderScripts($scripts); ?>

</body>
</html>
