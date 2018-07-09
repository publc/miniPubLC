<?php

namespace App\Api\Auth;

use App\Core\Auth\Auth;

class AuthAPI extends Auth
{
    public function post()
    {
        parent::login();
    }
}
