<?php

return function() {
    session_destroy();
    redirect(ROUTE_GET_HOMEPAGE);
};
