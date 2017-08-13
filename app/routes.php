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

$router->get('/public/(.*)', function($path) {

    $path = "/public/{$path}";

    echo $path;
    return;
    echo file_get_contents("/public/{$path}");
});




// ROUTE: Homepage
$router->get(ROUTES['home'], function() use ($db, $twig, $model) {

    echo $twig->render('homepage.twig', $model);

    return;
});



$router->get(ROUTES['register_admin'], function() use ($db, $model, $twig) {

    // MAKE SURE PEEPS CAN'T USE THIS UNLESS IT'S THE FIRST TIME THEY SET IT UP
    $stmt = $db->query("SELECT * FROM users WHERE user_type='admin'");
    $users = $stmt->fetch(PDO::FETCH_ASSOC);

    print_r($users);

    // IF THE # OF ADMIN USERS IS >0, OR THE USER IS NOT CURRENTLY LOGGED IN, BUG OUT
    if (!empty($users) || isset($_SESSION['user_name'])) {
        redirect(ROUTES['login']);
        echo 'ADMIN EXISTS';
        return;

    } else {
        $model['new_setup_message'] = "Welcome to [CLIENT-PORTFOLIO]!\nSince this appears to be a brand new instance, you must first setup your admin user profile to get started.";

    }


    $model['inputs'] = [
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

    echo $twig->render('register-admin.twig', $model);

    return;

});


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
        $res['notice'] = 'Admin user already exists';
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


// =========================================================================================================
//  USER SESSION PROCESSES
// =========================================================================================================

// GET ROUTE: user login

$router->get(ROUTES['login'], function() use ($db, $model, $twig) {

    $model['inputs'] = [
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


// POST ROUTE: user login
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
        $res['notice'] = 'User login credentials are not valid.';
        echo json_encode($res);
        return;
    }


    // if the passwords don't match, bug out
    if (!password_verify($user_password, $user['user_password'])) {
        $res['notice'] = 'User login credentials are not valid.';
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


// ROUTE: user logout
$router->get(ROUTES['logout'], function() use ($twig) {
    session_destroy();
    redirect(ROUTES['login']);
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


// ROUTE: admin dashboard
$router->get(ROUTES['admin_dashboard'], function() use ($db, $twig, $model) {

    $user = $_SESSION['user'];

    echo $twig->render('admin-dashboard.twig', $model);

    return;
});






// // ROUTE: register
// $router->get(ROUTES['register_client'], function() use ($model, $twig) {

//     $db = require Path::join(APP_DIR, 'db.php');

//     $stmt = $db->prepare('
//         SELECT *
//         FROM clients
//         ORDER BY name
//     ');

//     $stmt->execute();

//     $clients = $stmt->fetchAll();

//     $model['inputs'] = [
//         [
//             'label' => 'Email'
//         ,   'name'  => 'username'
//         ,   'type'  => 'email'
//         ,   'required' => true
//         ]
//     ,   [
//             'label' => 'Password'
//         ,   'name'  => 'password'
//         ,   'type'  => 'password'
//         ,   'required' => true
//         ]
//     ,   [
//             'label' => 'Confirm Password'
//         ,   'name'  => 'password-confirm'
//         ,   'type'  => 'password'
//         ,   'required' => true
//         ]
//     ,   [
//             'label' => 'Client'
//         ,   'name'  => 'client'
//         ,   'type'  => 'select'
//         ,   'opts'  => $clients
//         ,   'required' => true
//         ]
//     ];

//     $stmt   = null;
//     $db     = null;

//     echo $twig->render('register.twig', $model);

// });



// // ROUTE: register
// $router->post(ROUTES['register'], function() use ($model) {

//     // // check if user doesn't exist
//     // if (userExists()) {
//     //     // if user exists, return 'bleh'
//     //     return;
//     // }

//     $db = require Path::join(APP_DIR, 'db.php');

//     $stmt = $db->prepare('
//         SELECT * FROM users WHERE username=:username AND is_deleted=false
//     ');

//     $res = $stmt->execute([ ':username' => $_POST['username']]);

//     $stmt   = null;

//     if ($res) {
//         echo message("user already exists", 500);
//         return;
//     }


//     // move on to putting the user in the DB
//     $user = [
//         ':username'         => filter_var(trim($_POST['username']), FILTER_VALIDATE_STRING)
//     ,   ':password'         => filter_var(trim($_POST['password']), FILTER_VALIDATE_STRING)
//     ,   ':password-confirm' => filter_var(trim($_POST['password-confirm']), FILTER_VALIDATE_STRING)
//     ];


//     $stmt = $db->prepare('
//         INSERT INTO users (username, password, client)
//             VALUES (:username, :password, :client)
//             ;
//         ');

//     $stmt->execute($user);

//     $







//     // close DB connection
//     $db = null;

//     return;



//     // hash user password
//     // $hash   = password_hash($pass, PASSWORD_BCRYPT);
//     // $res    = password_verify($pass, $hash);

//     // registerUser($user);

//     //

// });


// // ROUTE: login
// $router->get(ROUTES['login'], function() use ($twig, $model) {

//     $pass   = "rasmuslerdorf";
//     $hash   = password_hash($pass, PASSWORD_BCRYPT);
//     $res    = password_verify($pass, $hash);

//     echo json_encode($res);
//     return;

//     $model = [];

//     echo $twig->render('login.twig', $model);
// });


// $router->post(ROUTES['login'], function() use ($model) {

//     print_r($_POST);

//     // look up user in users collection



//     // get users hash

//     // verify password against use rhash

//     // if verified -> navigate to homepage and list all projects

//     // else -> redirect to login page with error messsage
// });


// // ROUTE: logout
// $router->get(ROUTES['logout'], function() use ($twig) {
//     validateUser();
//     logoutUser();
//     return;
// });






// //
// $router->get('[a-z0-9_-]+', function($clientRoute) {

//     echo $clientRoute;
//     return;

// });



// Run it!
// --------------------------------------------------

$router->run();
