<?php

return function() {
    session_destroy();
    Debug('user logged out');
    redirect(ROUTE_GET_HOMEPAGE);
};
