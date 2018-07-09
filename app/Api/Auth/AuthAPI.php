<?php

namespace App\Api\Auth;

use App\Core\Auth\Auth;

class AuthAPI extends Auth
{
    public function login()
    {
        parent::login();
        return $this->response();
    }

    public function register()
    {
        parent::register();
        return $this->response();
    }

    public function patch()
    {
        parent::patch();
        return $this->response();
    }

    public function delete($params = array())
    {
        parent::delete();
        return $this->response();
    }

    protected function response()
    {
        $response = $this->response->withStatus(200)->withJSON(['errors' => false]);
        return $this->app->respond($response);
    }
}
