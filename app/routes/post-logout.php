<?php

// use Symfony\Component\Yaml\Yaml;

return function() {
    session_destroy();

    $res = [];

    $res['success'] = true;

    res_json($res);

};
