<?php


return [
    'displayErrorDetails'   => IS_DEV ? true : false,
    'db' => [
        'path'  => DATA_DIR . '/main.db',
        'type'  => 'sqlite',
    ],
];
