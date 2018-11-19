<?php

namespace App;


use \App\BaseController;


class ApiController extends BaseController {



    public function status($req, $res, $args) {
        return $res->withJson([
            'status' => 'success',
            'data'  => [
                'serverStatus' => 'up'
            ]
        ]);
    }
}
