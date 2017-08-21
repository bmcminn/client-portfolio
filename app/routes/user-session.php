<?php

// =========================================================================================================
//  USER SESSION PROCESSES
// =========================================================================================================


// USER LOGIN PAGE
// ------------------------------------------------------------

$router->get(ROUTES['login'], function() use ($model, $twig) {

    if (!isset($model['page'])) {
        $model['page'] = [];
    }

    $model['page']['title'] = 'User Login';

    $model['form'] = [];

    $model['form']['title']            = 'User Login';
    $model['form']['submitLabel']      = 'Login';
    $model['form']['id']               = 'user-login';
    $model['form']['forgotPassword']   = true;
    $model['form']['actionRoute']      = ROUTES['login'];

    $model['form']['fields'] = [
        [
            'label'     => 'User Email'
        ,   'type'      => 'email'
        ,   'required'  => true
        ,   'name'      => 'user_email'
        ]
    ,   [
            'label'         => 'Password'
        ,   'type'          => 'password'
        ,   'required'      => true
        ,   'name'          => 'user_password'
        ]
    ];

    echo $twig->render('user-login.twig', $model);

});


// USER LOGIN SUBMISSION
// ------------------------------------------------------------

$router->post(ROUTES['login'], function() use ($db) {

    // Set response header
    header('content-type: application/json');

    // Sanitize form submission data
    $user_email     = trim(filter_var($_POST['user_email'],      FILTER_SANITIZE_EMAIL));
    $user_password  = trim(filter_var($_POST['user_password'],   FILTER_SANITIZE_STRING));

    // Setup response collection
    $res = [];

    // Validate the fields aren't empty
    if (empty($user_email)) {
        $res['user_email'] = 'Field cannot be empty';
    }

    if (empty($user_password)) {
        $res['user_password'] = 'Field cannot be empty';
    }

    // Valide email formatting
    if (!preg_match('/.*@.*\..*/i', $user_email)) {
        $res['user_email'] = 'Email address is not correct format: <code>example@email.com</code>';
    }

    // if we have errors, return said errors
    if (!empty($res)) {
        echo json_encode($res);
        return;
    }

    // No erros means we look up the user
    $stmt = $db->query("SELECT * FROM `users` WHERE user_email='{$user_email}'");

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // if the user is not found
    if (empty($user)) {
        $res['notice'] = [
            'message'   => 'User login credentials are not valid.'
        ,   'level'     => 'danger'
        ];
        echo json_encode($res);
        return;
    }

    // if the passwords don't match, bug out
    if (!password_verify($user_password, $user['user_password'])) {
        $res['notice'] = [
            'message'   => 'User login credentials are not valid.'
        ,   'level'     => 'danger'
        ];
        echo json_encode($res);
        return;
    }

    // cleanup user model
    unset($user['user_password']);

    // add usermodel to session
    $_SESSION['user'] = $user;

    // reroute user to [user_type]_dashboard route
    $res['redirect'] = ROUTES["{$user['user_type']}_dashboard"];

    echo json_encode($res);
    return;

});



// USER LOGOUT/SESSION REMOVAL
// ------------------------------------------------------------

$router->get(ROUTES['logout'], function() use ($twig) {
    session_destroy();
    redirect(ROUTES['login']);
    return;
});

