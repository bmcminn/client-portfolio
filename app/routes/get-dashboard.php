<?php

return function() {
    isLoggedIn();

    $req = req();

    require(VIEWS_DIR . '/app.twig');

    // print_r($_SERVER);
};
