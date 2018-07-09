<?php

namespace App\Core;

use App\Core\Contracts\APIInterface;

class API implements APIInterface
{
    protected $controller;

    protected $controllerClass;

    public function __construct()
    {
        if (empty($this->controller)) {
            $controller = explode("\\", get_class($this));
            $this->controller = ucwords(str_replace("API", "", $controller[count($controller) - 1]));
        }

        $controller = "\App\Controllers\\" . ucwords($this->controller) . 'Controller';
        if (class_exists($controller)) {
            $this->controllerClass = new $controller;
        }
    }


    public function get($request, $response, ...$params)
    {
        return $this->controllerClass->get();
    }

    public function post($request, $response, ...$params)
    {
        return $this->controllerClass->find();
    }

    public function put($request, $response, ...$params)
    {
        return $this->controllerClass->create();
    }

    public function patch($request, $response, ...$params)
    {
        return $this->controllerClass->update();
    }

    public function delete($request, $response, ...$params)
    {
        return $this->controllerClass->delete();
    }
}
