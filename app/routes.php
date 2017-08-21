<?php

// Create a Router
// --------------------------------------------------

use Webmozart\PathUtil\Path;

$db     = require('db.php');
$router = new \Bramus\Router\Router();


// Custom 404 Handler
$router->set404(function() use ($twig) {
    header('HTTP/1.1 404 Not Found');
    echo '404, route not found!';
});







// Define routes
// --------------------------------------------------

$router->get("{ROUTES['static']}/(.*)", function($path) {

    $path = Path::canonicalize("${ROUTES['static']}/${path}");

    echo $path;
    // return;
    echo file_get_contents($path);
});




// ROUTE: Homepage
$router->get(ROUTES['home'], function() use ($db, $twig, $model) {

    if (!isset($model['page'])) {
        $model['page'] = [];
    }

    // $model['page']['title'] = 'New Client Registration';

    echo $twig->render('homepage.twig', $model);

    return;
});







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

    $model['form']['title']            = 'Register Admin';
    $model['form']['submitLabel']      = 'Register';
    $model['form']['id']               = 'register-admin';
    $model['form']['actionRoute']      = ROUTES['register_admin'];

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


    $res = [];

    // Validate form data
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




// USER FORGOT PASSWORD PAGE
// ------------------------------------------------------------

$router->get(ROUTES['forgot_password'], function() use ($model, $twig) {


    if (!isset($model['page'])) {
        $model['page'] = [];
    }

    $model['page']['title'] = 'Forgot Password';



    $model['form'] = [];

    $model['form']['title']             = $model['page']['title'];
    $model['form']['submitLabel']       = 'Submit';
    $model['form']['id']                = 'user-login';
    $model['form']['actionRoute']       = ROUTES['forgot_password'];
    $model['form']['noticeAnimation']   = 'flash';

    $model['form']['fields'] = [
        [
            'label'     => 'User Email'
        ,   'type'      => 'email'
        ,   'required'  => true
        ,   'name'      => 'user_email'
        ]
    ];


    echo $twig->render('user-login.twig', $model);

});



// USER FORGOT PASSWORD SUBMISSION
// ------------------------------------------------------------

$router->post(ROUTES['forgot_password'], function() use ($db, $model, $twig) {

    // Set response header
    header('content-type: application/json');


    // Sanitize form submission data
    $user_email = trim(filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL));


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
    $stmt = $db->query("SELECT user_id, user_email, user_type, user_fullname FROM `users` WHERE user_email='{$user_email}'");

    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    // Lets alert the user saying that we'll notify the email provided if it exists in our system
    $res['notice'] = [
        'message'   => 'Thank you, an email will be sent to the address provided, if the user exists in our system.'
    ,   'level'     => 'info'
    ];


    // if we need to send a password reset email
    if (!empty($user)) {

        $range      = 1000000000;
        $timestamp  = time() + (1000*60*60*24*3);
        $hashid     = $timestamp + random_int($range, $range*10);
        $resetToken = hash('sha256', $hashid);

        // logger(hash('sha256', $hashid));

        $model['user'] = $user;

        $to         = $user['user_email'];
        $subject    = 'Password Reset';

        $model['email'] = [
            'to'            => $to
        ,   'subject'       => $subject
        ,   'reset_token'   => $resetToken
        ];

        $msg = $twig->render('emails/forgot-password.twig', $model);

        $headers = implode("\r\n", [
            'MIME-Version: 1.0'
        ,   'From: admin@client-portfolio.com'
        ,   'Content-type: text/html; charset=iso-8859-1'
        ]);

        // TODO: [SECURITY] remove email headers that could leak details about the sending server
        mail($to, $subject, $msg, $headers);


        // file_put_contents(Path::canonicalize(DATA_DIR, "/resets/${timestamp}.php"), implode("\n", [
        file_put_contents("app/data/resets/__${resetToken}.php", implode("\n", [
            '<?php'
        ,   'return ['
        ,   "    'user_id' => '${user['user_id']}'"
        ,   "    'reset_expires' => '${timestamp}'"
        ,   "    'reset_token' => '${resetToken}'"
        ,   '];'
        ]));

        // TODO: Still not sure why this particular query timesout so bad...
        // // PREPARE OUR SQL FOR INSERTING NEW RESET RECORD
        // $ins = $db->prepare("INSERT INTO `password_resets`(user_id, reset_token, reset_expires) VALUES(:user_id, :reset_token, :reset_expires)");

        // // Assign user values, but trim trailing whitespace on each of them
        // $ins->bindValue(':user_id',        $user['user_id']);
        // $ins->bindValue(':reset_token',    $resetToken);
        // $ins->bindValue(':reset_expires',  $timestamp);

        // $ins->execute();

        // $blah = $ins->lastInsertId();
    }


    echo json_encode($res);
    return;

});



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


    $model['page']['title'] = 'Password Reset';

    $model['form'] = [];

    $model['form']['title']             = $model['page']['title'];
    $model['form']['submitLabel']       = 'Submit';
    $model['form']['id']                = 'user-login';
    $model['form']['actionRoute']       = ROUTES['reset_password'];
    $model['form']['noticeAnimation']   = 'flash';

    $model['form']['fields'] = [
        [
            'label'     => 'User Email'
        ,   'type'      => 'email'
        ,   'required'  => true
        ,   'name'      => 'user_email'
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










// =========================================================================================================
//  USER/ADMIN DASHBOARD VIEWS
// =========================================================================================================


// ROUTE: user dashboard
$router->get(ROUTES['user_dashboard'], function() use ($db, $twig, $model) {

    $user = $_SESSION['user'];

    $twig->render("{$user['user_type']}-dashboard.twig", $model);

});





// ===========================================================================

// ROUTE: admin dashboard
$router->get(ROUTES['admin_dashboard'], function() use ($db, $twig, $model) {

    $user = $_SESSION['user'];


    // DB call to get clients
    $stmt = $db->query("SELECT * FROM users WHERE user_type='client'");
    $clients = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($users)) {
        $model['clients'] = $clients;
    }

    echo $twig->render('admin-dashboard.twig', $model);

    return;
});





// TODO: register routes for testing email templates when env is not prod
//  - /test
//      - /emails : list of email templates available to view
//      - /emails/(template-name)

$router->get('/test/emails', function() use ($twig, $model) {

    $files = glob('app/views/emails/*.twig');

    $templates = [];

    foreach ($files as $file) {
        $filename = basename($file);
        $filename = preg_replace('/\..+$/', '', $filename);

        $templates[] = [
            'route' => "/test/emails/${filename}"
        ,   'title' => preg_replace('/[\-]/i', ' ', $filename)
        ];
    }

    $model['templates'] = $templates;

    echo $twig->render('test/emails.twig', $model);
});




$router->get('/test/emails/(.*)', function($template) use ($twig, $model) {

    echo $twig->render("emails/${template}.twig", $model);

});





// Run it!
// --------------------------------------------------

$router->run();
