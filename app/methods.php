<?php


use Webmozart\PathUtil\Path;



function validateUser() {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . ROUTES['login']);
    }
}


function logoutUser() {
    session_destroy();
    header('Location: ' . ROUTES['home']);
}


function message($msg, $status=200) {
    $res = [];

    $res['message'] = $msg;
    $res['status']  = $status;

    return json_encode($res);
}


function userExists() {

    global $db;

    $contract = [
        'username' => FILTER_SANITIZE_ENCODED
    // ,   'password' => FILTER_SANITIZE_ENCODED
    // ,   'password-confirm' => FILTER_SANITIZE_ENCODED
    ];

    $data = validateData($_POST, $contract);

    $userQuery = "SELECT * FROM users WHERE username=:username";


    try {
        $stmt = $db->prepare($userQuery);
        $stmt->execute($data);

    } catch (PDOExecption $e){
        echo $e->getMessage();
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return;
    }

    return $user;
}


function validateData($data, $contract) {
    $data = filter_var_array($data, $contract);

    $res = [];

    foreach ($data as $key => $value) {
        $res[":${key}"] = $value;
    }

    return $res;
}


function validateUserParams() {



}



function hashPassword($password, $opts = []) {
    $options = array_replace_recursive([
        'cost' => 12
    ], $opts);

    return password_hash($password, PASSWORD_BCRYPT, $options);
}




function redirect($route = '/') {
    header("location: {$route}");
}



function registerClients() {

    $filepath = Path::join(DATA_DIR, 'data.clients.json');

    if (!cacheBusted('registerClients', 0)) {
        return file_get_contents($filepath);
    }

    $folders = glob(Path::join(PROJECT_DIR, '*'), GLOB_ONLYDIR);

    $clients = [];

    foreach ($folders as $key => $folder) {
        // echo $folder . "\n";
        $client = [];

        $client['filepath'] = $folder;
        $client['folder']   = substr($folder, strlen(PROJECT_DIR) + 1);

        $name = preg_replace('/[-_]/i', ' ', $client['folder']);
        $client['name']     = ucwords($name);

        $files = glob_recursive(Path::join($folder, '*'));

        print_r($files);

        $client['downloads'] = [];
        $client['images'] = [];
        $client['config'] = [];

        foreach ($files as $key => $file) {
            if (preg_match('/\.zip$/i', $file)) {
                array_push($client['downloads'], [
                    'name' => basename($file)
                ,   'path' => substr(strlen(BASE_DIR, $file))
                ]);
            }
        }

        array_push($clients, $client);
    }

    print_r($clients);

    file_put_contents($filepath, json_encode($clients));

}



function glob_recursive($pattern, $flags=0) {
    $files = glob($pattern, $flags);

    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }

    return $files;
}



/**
 * Defines a cache marker timestamp for triggering certain service checks
 * @param  string  $id     ID for a given trigger to check
 * @param  integer $offset Time offset in minutes the cache should be checked for
 * @return bool            True if the cache needs to be busted and reset
 */
function cacheBusted($id, $offset=30) {

    $now    = time();
    $offset = $offset * 60;
    $bust   = false;

    $filepath = Path::join(DATA_DIR, "cache.${id}.php");

    // if timestamp cache exists, read its contents
    if (file_exists($filepath)) {
        $timestamp = require $filepath;

    // else, lets set things up to write a new file
    } else {
        $bust = true;
        $timestamp = $now;
    }


    // $debug = [
    //     'name'  => $id
    // ,   'offset' => $offset
    // ,   'time'  => $timestamp
    // ,   'now'   => $now
    // ];

    // print_r($debug);


    // bust cache
    if ($bust || ($timestamp + $offset) < $now) {
        file_put_contents($filepath, "<?php return ${now};");
        return true;
    }


    // no need to bust cache
    return false;
}
