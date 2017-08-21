<?php



// ======================================================================
//  ADMIN REGISTRATION
// ======================================================================

$router->get(ROUTES['register_admin'], function() use ($db, $model, $twig) {

    // MAKE SURE PEEPS CAN'T USE THIS UNLESS IT'S THE FIRST TIME THEY SET IT UP
    $stmt = $db->query("SELECT * FROM users WHERE user_type='admin'");
    $users = $stmt->fetch(PDO::FETCH_ASSOC);

    // IF THE # OF ADMIN USERS IS >0, OR THE USER IS NOT CURRENTLY LOGGED IN, BUG OUT
    if (!isset($_SESSION['user']) && !empty($users)) {
        redirect(ROUTES['login']);
        echo 'ADMIN EXISTS';
        return;
    }


    if (empty($users)) {
        $model['new_setup_message'] = "Welcome to [CLIENT-PORTFOLIO]!<br><br>Since this appears to be a brand new instance, you must first setup your admin user profile to get started.";
    }


    $model['form'] = [];

    $model['form']['title']         = 'Register Admin';
    $model['form']['submitLabel']   = 'Register';
    $model['form']['id']            = 'register-admin';
    $model['form']['actionRoute']   = ROUTES['register_admin'];

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
            'label'         => 'Password'
        ,   'type'          => 'password'
        ,   'required'      => true
        ,   'name'          => 'user_password'
        ]
    ,   [
            'label'         => 'Password Confirm'
        ,   'type'          => 'password'
        ,   'required'      => true
        ,   'name'          => 'user_password_confirm'
        ]
    ,   [
            'label'         => 'Password Confirm'
        ,   'type'          => 'hidden'
        ,   'name'          => 'user_type'
        ,   'value'         => 'admin'
        ]
    ];

    echo $twig->render('register-user.twig', $model);

    return;

});





// ===========================================================================

// POST: register admin queries for setting up new admin users
$router->post(ROUTES['register_admin'], function() use ($db) {

    // Sanitize form submission data
    $user_fullname          = trim(filter_var($_POST['user_fullname'],           FILTER_SANITIZE_STRING));
    $user_email             = trim(filter_var($_POST['user_email'],              FILTER_SANITIZE_EMAIL));
    $user_password          = trim(filter_var($_POST['user_password'],           FILTER_SANITIZE_STRING));
    $user_password_confirm  = trim(filter_var($_POST['user_password_confirm'],   FILTER_SANITIZE_STRING));
    $user_type              = trim(filter_var($_POST['user_type'],               FILTER_SANITIZE_STRING));

    $res = [];

    // CHECK IF FIELDS ARE FILLED OUT COMPLETELY
    if (empty($user_fullname)) {
        $res['user_fullname'] = 'Field cannot be empty';
    }

    if (empty($user_email)) {
        $res['user_email'] = 'Field cannot be empty';
    }

    if (empty($user_password)) {
        $res['user_password'] = 'Field cannot be empty';
    }

    if (empty($user_password_confirm)) {
        $res['user_password_confirm'] = 'Field cannot be empty';
    }

    if (empty($user_type)) {
        $res['user_type'] = 'Field cannot be empty';
    }


    // CHECK IF PASSWORDS MATCH
    if ($user_password !== $user_password_confirm) {
        $res['user_password']           = 'Passwords do not match';
        $res['user_password_confirm']   = 'Passwords do not match';
    }


    // CHECK IF EMAIL IS VALID
    if (!preg_match('/.*@.*\..*/i', $user_email)) {
        $res['user_email'] = 'Email address is not correct format: <code>example@email.com</code>';
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
    $stmt = $db->prepare("INSERT INTO `users`(user_email, user_fullname, user_password, user_type) VALUES(:user_email, :user_fullname, :user_password, :user_type)");

    // Assign user values, but trim trailing whitespace on each of them
    $stmt->bindValue(':user_email',     $user_email);
    $stmt->bindValue(':user_fullname',  $user_fullname);
    $stmt->bindValue(':user_password',  $user_password);
    $stmt->bindValue(':user_type',      $user_type);

    $stmt->execute();


    // RETURN THE ROUTE THE FORM SHOULD REDIRECT TO
    $res['redirect'] = ROUTES['login'];
    echo json_encode($res);

    return;

});
