<?php

namespace App\Api\Auth;

use App\Core\Auth\Auth;

class AuthAPI extends Auth
{
    public function login()
    {
        $response = parent::login();
        return $this->response($response);
    }

    public function register()
    {
        $response = parent::register();
        return $this->response($response);
    }

    public function patch()
    {
        $response = parent::patch();
        return $this->response($response);
    }

    public function delete($params = array())
    {
        $response = parent::delete();
        return $this->response($response);
    }

    public function logout()
    {
        $response = parent::logout();
        return $this->response($response);
    }

    protected function response($response)
    {
        return $this->app->respond($this->response->withStatus(200)->withJSON($response));
    }
}
