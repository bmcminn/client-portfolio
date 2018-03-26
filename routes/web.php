<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });


$router->get('/', function() use ($router) {
    return 'HELLO';
});


$app->post('login/', ['middleware' => 'auth', 'uses' => 'UsersController@authenticate']);



$router->group([
    'prefix' => 'api/v1'
], function() use ($router) {

    $router->get('/', 'ApiController@index');

});
