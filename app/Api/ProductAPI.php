<?php

namespace App\Api;

use App\Core\API;

class ProductAPI extends API
{

    protected $controller = 'home';

    public function get($request, $response, ...$params)
    {
        $users = parent::get($request, $response, ...$params);
        return $response->withJSON($users);
    }
}
