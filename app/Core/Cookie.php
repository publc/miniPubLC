<?php

namespace App\Core;

use App\Core\Container;

class Cookie
{
    protected $container;

    public function __construct()
    {
        $this->container = new Container([
            'config' => function () {
                return new Config;
            },
            'hash' => function () {
                return new Hash;
            }
        ]);
    }


    public function exists($name)
    {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    public function get($name)
    {
        return ($this->exists($name)) ? $_COOKIE[$name] : false;
    }

    public function put($name = null, $value = null, $expiry = null)
    {
        $config = $this->container->config->get('remember');
        $hash = $this->container->hash;

        $name = (is_null($name)) ? $config->cookie_name : $name;
        $value = (is_null($value)) ? $hash->unique() : $value;
        $expiry = (is_null($expiry)) ? $config->cookie_expire : $expiry;

        return (setcookie($name, $value, time() + $expiry, "/")) ? true : false;
    }

    public function delete($name)
    {
        $this->put($name, "", - 60);
    }
}
