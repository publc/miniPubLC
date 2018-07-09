<?php

namespace App\Core;

class Config
{

    public static function get($path = null)
    {
        if (is_null($path)) {
            return;
        }

        $config = require "../config/config.php";
        $path = explode(".", $path);

        foreach ($path as $bit) {
            if (isset($config[$bit])) {
                $response = $config[$bit];
            }
        }

        return json_decode(json_encode($response));
    }
}
