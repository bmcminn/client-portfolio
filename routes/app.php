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

// TODO: figure out how to redirect user to login screen if not authorized
// $app->group([
//     'middleware' => 'api.auth',
// ], function ($app) {

    $app->get('/', [
        'uses'  => 'AppController@homepageView',
        'as'    => 'app.view.homepage'
    ]);

    $app->get('/login', [
        'uses'  => 'AppController@loginView',
        'as'    => 'app.view.login'
    ]);

    $app->get('/dashboard', [
        'uses'  => 'AppController@dashboardView',
        'as'    => 'app.view.dashboard'
    ]);

// });
