<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;

class AppController extends Controller
{

    private $_baseModel = [];


    function __construct() {

        $this->_baseModel = [
            'lang'  => env('APP_LOCALE'),
            'debug' => !!env('APP_DEBUG', 0),

            'ga' => [
                'tracking' => env('APP_GA_TRACKING', 'UA-XXXXX-Y')
            ],
        ];

    }


    public function homepageView(Application $app) {
        return "homepage";
    }

    public function loginView(Application $app) {

        $model = $this->_model([
            'name' => 'pants'
        ]);

        return view('login', $model);

    }

    public function dashboardView(Application $app) {
        return "dashboard";
    }



    private function _model(array $model) {
        return array_replace_recursive($this->_baseModel, $model);
    }

}
