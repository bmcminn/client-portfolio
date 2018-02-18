<?php

return function() {
    $req = req();
    echo json_encode($req);
    // print_r($_SERVER);
};
