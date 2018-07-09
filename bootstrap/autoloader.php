<?php

namespace Bootstrap;

class Autoloader
{

    public static function register()
    {
        spl_autoload_register(function ($class) {
            $class = explode("\\", $class);
            $class[0] = strtolower($class[0]);
            $class = implode("/", $class);
            $file =  "../" . $class . '.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }

            return false;
        });
    }
}
