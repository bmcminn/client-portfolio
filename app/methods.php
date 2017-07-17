<?php


function validateUser() {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . ROUTES['login']);
    }
}



function logoutUser() {
    session_destroy();
    header('Location: ' . ROUTES['home']);
}
