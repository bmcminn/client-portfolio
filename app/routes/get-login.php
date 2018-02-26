<?php

return function() {

    // if user is already logged in,
    if (isLoggedIn(false)) {
        redirect(ROUTE_GET_DASHBOARD);
    }

    require(VIEWS_DIR . '/login.twig');
};
