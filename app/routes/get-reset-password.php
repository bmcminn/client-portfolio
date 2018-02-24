<?php

return function($req=[]) {

    $LOG_LABEL = '[GET PASSWORD RESET]';

    $req = req($req);

    if (isset($req['hash'])) {
        $hash = $req['hash'];

        $req['hash_exists'] = hash_exists($hash);

        require(VIEWS_DIR . '/reset-password.twig');

        return;
    }

    require(VIEWS_DIR . '/reset-password-request.twig');
};


