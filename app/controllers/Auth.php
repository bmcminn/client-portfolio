<?php

namespace App;


use \Firebase\JWT\JWT;


class Auth {

    protected static $_algo;
    protected static $instance;


    public static function init() {
        if (self::$instance) {
            return;
        }

        self::$_algo = env('JWT_ALGO', 'hs256');
    }


    public static function generateToken($data) {

        $now = time();

        $secret = env('JWT_SECRET');

        $token = [];

        $token['iss'] = env('HOST_DOMAIN');
        $token['aud'] = env('HOST_DOMAIN');
        $token['iat'] = $now;
        $token['nbf'] = $now - minutes(0.5);
        $token['sub'] = $data;

        $jwt = JWT::encode($token, $secret);

        return $jwt;
    }


    // TODO: setup password reset utility
    // $router->post(ROUTES['forgot_password'], function() use ($db, $model, $twig) {

    //     // Set response header
    //     header('content-type: application/json');


    //     // Sanitize form submission data
    //     $user_email = trim(filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL));


    //     $res = [];

    //     // Validate form data
    //     if (empty($user_email)) {
    //         $res['user_email'] = 'Field cannot be empty';
    //     }

    //     // Valide email formatting
    //     if (!preg_match('/.*@[\w\d].*\..*/i', $user_email)) {
    //         $res['user_email'] = 'Email address is not correct format: <code>example@email.com</code>';
    //     }

    //     // if we have errors, return said errors
    //     if (!empty($res)) {
    //         echo json_encode($res);
    //         return;
    //     }


    //     // No erros means we look up the user
    //     $stmt = $db->query("SELECT user_id, user_email, user_type, user_fullname FROM `users` WHERE user_email='{$user_email}'");

    //     $user = $stmt->fetch(PDO::FETCH_ASSOC);


    //     // Lets alert the user saying that we'll notify the email provided if it exists in our system
    //     $res['notice'] = [
    //         'message'   => 'Thank you, an email will be sent to the address provided, if the user exists in our system.'
    //     ,   'level'     => 'info'
    //     ];


    //     // if we need to send a password reset email
    //     if (!empty($user)) {

    //         $range      = 1000000000;
    //         $timestamp  = time() + (1000*60*60*24*3);
    //         $hashid     = $timestamp + random_int($range, $range*10);
    //         $resetToken = hash('sha256', $hashid);

    //         // logger(hash('sha256', $hashid));

    //         $model['user'] = $user;

    //         $to         = $user['user_email'];
    //         $subject    = 'Password Reset';

    //         $model['email'] = [
    //             'to'            => $to
    //         ,   'subject'       => $subject
    //         ,   'reset_token'   => $resetToken
    //         ];

    //         $msg = $twig->render('emails/forgot-password.twig', $model);

    //         $headers = implode("\r\n", [
    //             'MIME-Version: 1.0'
    //         ,   'From: admin@client-portfolio.com'
    //         ,   'Content-type: text/html; charset=iso-8859-1'
    //         ]);

    //         // TODO: [SECURITY] remove email headers that could leak details about the sending server
    //         mail($to, $subject, $msg, $headers);


    //         // file_put_contents(Path::canonicalize(DATA_DIR, "/resets/${timestamp}.php"), implode("\n", [
    //         file_put_contents("app/data/resets/__${resetToken}.php", implode("\n", [
    //             '<?php'
    //         ,   'return ['
    //         ,   "    'user_id' => '${user['user_id']}'"
    //         ,   ",   'reset_expires' => '${timestamp}'"
    //         ,   ",   'reset_token' => '${resetToken}'"
    //         ,   '];'
    //         ]));

    //         // TODO: Still not sure why this particular query timesout so bad...
    //         // // PREPARE OUR SQL FOR INSERTING NEW RESET RECORD
    //         // $ins = $db->prepare("INSERT INTO `password_resets`(user_id, reset_token, reset_expires) VALUES(:user_id, :reset_token, :reset_expires)");

    //         // // Assign user values, but trim trailing whitespace on each of them
    //         // $ins->bindValue(':user_id',        $user['user_id']);
    //         // $ins->bindValue(':reset_token',    $resetToken);
    //         // $ins->bindValue(':reset_expires',  $timestamp);

    //         // $ins->execute();

    //         // $blah = $ins->lastInsertId();
    //     }


    //     echo json_encode($res);
    //     return;

    // });




}


Auth::init();
