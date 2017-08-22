<?php

use Webmozart\PathUtil\Path;



// RESET PASSSWORD PAGE
// ------------------------------------------------------------

$router->get(ROUTES['reset_password'], function() use ($model, $twig) {


    if (!isset($model['page'])) {
        $model['page'] = [];
    }

    // if the reset_token isn't provided, don't let the user play around with this page
    isset($_GET['reset_token'])
        ? $_GET['reset_token']
        : redirect(ROUTES['home'])
        ;


    if ($_GET['reset_token'] === '') {
        redirect(ROUTES['home']);
    }


    $resetData = require(Path::join(__DIR__, '../data/resets', '__'.$_GET['reset_token'].'.php'));

    // has the reset expired
    if ($resetData['reset_expires'] < time()) {
        redirect(ROUTES['password_reset']);
    }

    logger($resetData);


    $model['page']['title'] = 'Set Password';

    $model['form'] = [];

    $model['form']['title']             = $model['page']['title'];
    $model['form']['submitLabel']       = 'Submit';
    $model['form']['id']                = 'password-reset';
    $model['form']['actionRoute']       = ROUTES['reset_password'];
    $model['form']['noticeAnimation']   = 'flash';

    $model['form']['fields'] = [
        [
            'label'     => 'New Password'
        ,   'type'      => 'password'
        ,   'required'  => true
        ,   'name'      => 'user_password'
        ]
    ,   [
            'label'     => 'Confirm New Password'
        ,   'type'      => 'password'
        ,   'required'  => true
        ,   'name'      => 'user_password_confirm'
        ]
    ,   [
            'type'  => 'hidden'
        ,   'value' => isset($_GET['reset_token']) ? $_GET['reset_token'] : ''
        ,
        ]
    ];


    echo $twig->render('user-login.twig', $model);

});



// RESET PASSWORD SUBMISSION
// ------------------------------------------------------------

$router->post(ROUTES['reset_password'], function() use ($db) {

    // Set response header
    header('content-type: application/json');


    // Sanitize form submission data
    $user_email = trim(filter_var($_POST['user_email'],      FILTER_SANITIZE_EMAIL));


    $res = [];

    // Validate form data
    if (empty($user_email)) {
        $res['user_email'] = 'Field cannot be empty';
    }

    // Valide email formatting
    if (!preg_match('/.*@[\w\d].*\..*/i', $user_email)) {
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


    // TODO: hookup user email submission handler


    // Lets alert the user saying that we'll notify the email provided if it exists in our system
    $res['notice'] = [
        'message'   => 'Thank you, an email will be sent to the email provided if the user exists in our system.'
    ,   'level'     => 'info'
    ];


    echo json_encode($res);
    return;

});

