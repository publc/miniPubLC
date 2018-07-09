<?php

namespace App\Core;

use Exception;
use App\Core\Container;

class Token
{
    protected $container;

    protected $config;

    protected $sessio;

    public function __construct()
    {
        $this->container = new Container([
            'config' => function () {
                return new Config;
            },
            'session' => function () {
                return new Session;
            }
        ]);

        $this->config = $this->container->config->get('session');
        $this->session = $this->container->session;
    }




    public function generate()
    {
        return $this->session->put($this->config->token_name, md5(uniqid()));
    }

    public function check($token)
    {
        $tokenName = $this->config->token_name;
        if ($this->session->exists($tokenName) && $this->session->get($tokenName) === $token) {
            $this->session->delete($tokenName);
            return true;
        }

        throw new Exception("Error Processing Request. Token CSFR validation fails", 1);
    }
}
