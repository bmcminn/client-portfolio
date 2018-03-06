<?php

return function() {
    Info('User cache re-up\'d');
    cacheUsers();
    // redirect(ROUTE_GET_HOMEPAGE);
};
