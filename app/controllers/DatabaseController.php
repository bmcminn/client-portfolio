<?php

namespace App;


use \App\BaseController;


class DatabaseController extends BaseController {


    public function InitDB($req, $res, $args) {

        // TODO: add in actual user lookup via User class
        $data = [
            'accountId' => $id,
            'name'      => 'Bob Vila',
        ];

        return $res->withJson($data);

    })->setName('api.user.id');


}
