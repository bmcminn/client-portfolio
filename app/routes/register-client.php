<?php


// ======================================================================
//  CLIENT REGISTRATION
// ======================================================================

$router->get(ROUTES['register_client'], function() use ($db, $model, $twig) {

    // IF THE # OF CLIENT USERS IS >0, OR THE USER IS NOT CURRENTLY LOGGED IN, BUG OUT
    if (!isset($_SESSION['user'])) {
        redirect(ROUTES['login']);
        echo 'CLIENT EXISTS';
        return;
    }


    if (!isset($model['page'])) {
        $model['page'] = [];
    }

    $model['page']['title'] = 'New Client Registration';



    $model['form'] = [];

    $model['form']['title']            = 'Register Client';
    $model['form']['submitLabel']      = 'Register';
    $model['form']['id']               = 'register-client';
    $model['form']['actionRoute']      = ROUTES['register_client'];

    $model['form']['fields'] = [
        [
            'label'         => 'Full Name'
        ,   'type'          => 'text'
        ,   'required'      => true
        ,   'name'          => 'user_fullname'
        ]
    ,   [
            'label'         => 'Email Address'
        ,   'type'          => 'text'
        ,   'placeholder'   => 'example@email.com'
        ,   'required'      => true
        ,   'name'          => 'user_email'
    ,   ]
    ,   [
            'label'         => 'Phone Number'
        ,   'type'          => 'tel'
        ,   'placeholder'   => '512-555-1234'
        ,   'required'      => true
        ,   'name'          => 'user_phone'
    ,   ]
    // ,   [
    //         'label'         => 'Password'
    //     ,   'type'          => 'password'
    //     ,   'required'      => true
    //     ,   'name'          => 'user_password'
    //     ]
    // ,   [
    //         'label'         => 'Password Confirm'
    //     ,   'type'          => 'password'
    //     ,   'required'      => true
    //     ,   'name'          => 'user_password_confirm'
    //     ]
    ];

    echo $twig->render('register-user.twig', $model);

    return;

});


// ===========================================================================

// POST: register admin queries for setting up new admin users
$router->post(ROUTES['register_client'], function() use ($db) {

    // Sanitize form submission data
    $user_fullname          = trim(filter_var($_POST['user_fullname'],           FILTER_SANITIZE_STRING));
    $user_email             = trim(filter_var($_POST['user_email'],              FILTER_SANITIZE_EMAIL));
    // $user_password          = trim(filter_var($_POST['user_password'],           FILTER_SANITIZE_STRING));
    // $user_password_confirm  = trim(filter_var($_POST['user_password_confirm'],   FILTER_SANITIZE_STRING));
    $user_type              = trim(filter_var($_POST['user_type'],               FILTER_SANITIZE_STRING));
    $user_phone             = trim(filter_var($_POST['user_phone'],              FILTER_SANITIZE_STRING));

    $res = [];

    // CHECK IF FIELDS ARE FILLED OUT COMPLETELY
    if (empty($user_fullname)) {
        $res['user_fullname'] = 'Field cannot be empty';
    }

    if (empty($user_email)) {
        $res['user_email'] = 'Field cannot be empty';
    }

    // if (empty($user_password)) {
    //     $res['user_password'] = 'Field cannot be empty';
    // }

    // if (empty($user_password_confirm)) {
    //     $res['user_password_confirm'] = 'Field cannot be empty';
    // }

    if (empty($user_type)) {
        $res['user_type'] = 'Field cannot be empty';
    }

    if (empty($user_phone)) {
        $res['user_phone'] = 'Field cannot be empty';
    }


    // // CHECK IF PASSWORDS MATCH
    // if ($user_password !== $user_password_confirm) {
    //     $res['user_password']           = 'Passwords do not match';
    //     $res['user_password_confirm']   = 'Passwords do not match';
    // }


    // CHECK IF EMAIL IS VALID
    if (!preg_match('/.*@.*\..*/i', $user_email)) {
        $res['user_email'] = 'Email address is not correct format: <code>example@email.com</code>';
    }

    // CHECK IF PHONE IS VALID
    if (preg_match('/[_=\{\}\[\]\w]+/i', $user_phone)) {
        $res['user_phone'] = 'Phone number is not in correct format: <code>+1 512-555-1234</code>';
    }


    // setup response content type as JSON
    header('content-type: json');

    // IF THERE ARE ERRORS, SEND THE INFO BACK TO THE USER
    if (!empty($res)) {
        echo json_encode($res);
        return;
    }


    // VALIDATE IF THE USER ALREADY EXISTS
    $stmt = $db->query("SELECT * FROM users WHERE user_type='admin' AND user_email='{$user_email}'");
    $users = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($users)) {
        $res['notice'] = [
            'message'   => 'Admin user already exists'
        ,   'level'     => 'danger'
        ];

        echo json_encode($res);
        return;
    }


    // HASH OUR USER PASSWORD
    $user_password = hashPassword($user_password);


    // PREPARE OUR SQL FOR INSERTING NEW USER
    $stmt = $db->prepare("INSERT INTO `users`(user_email, user_fullname, user_type) VALUES(:user_email, :user_fullname, :user_type)");

    // Assign user values, but trim trailing whitespace on each of them
    $stmt->bindValue(':user_email',     $user_email);
    $stmt->bindValue(':user_fullname',  $user_fullname);
    // $stmt->bindValue(':user_password',  $user_password);
    $stmt->bindValue(':user_type',      $user_type);

    $stmt->execute();


    // RETURN THE ROUTE THE FORM SHOULD REDIRECT TO
    $res['redirect'] = ROUTES['login'];
    echo json_encode($res);

    return;

});

