<?php

namespace App\Core;

use App\Core\Container;

class Hash
{
    protected $container;

    public function __construct()
    {
        $this->container = new Container([
            'config' => function () {
                return new Config;
            }
        ]);
    }



    public function make($string)
    {
        $config = $this->container->config->get('hash');
        $options = ["cost" => $config->cost];
        return password_hash($string, $config->algo, $options);
    }

    public function unique()
    {
        return $this->make(uniqid());
    }
}
