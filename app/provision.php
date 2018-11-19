<?php


$dirs = [
    VIEWS_DIR,
    CACHE_DIR,
    DATA_DIR,
    LOGS_DIR,
    PROJECTS_DIR,
];


foreach ($dirs as $i => $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0700, true);
    }
}

