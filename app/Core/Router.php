<?php

namespace App\Core;

use App\Core\App;
use App\Core\Response;
use App\Core\Exceptions\RouteNotFoundException;
use App\Core\Exceptions\MethodNotAllowedException;
use App\Core\Exceptions\InvalidRequestParamsException;

class Router
{

    protected $path;

    protected $uris = [];

    protected $routes = [];

    protected $methods = [];

    protected $params = [];

    protected $expectedParams = [];

    public function setPath($path = '/')
    {
        foreach ($this->routes as $uri => $value) {
            if ($uri === strtolower($path)) {
                $this->path = $uri;
                return;
            }
        }

        foreach ($this->expectedParams as $uri => $value) {
            $match = str_replace('/', '\/', $uri);
            $match = '/(^' . $match . ')/';
            if (preg_match($match, $path)) {
                $validator = str_replace($uri, '', $path);
            }
            if (!empty($validator) && $validator[0] === '/') {
                $this->path = $uri;
                $this->setParams($uri, $path);
                return;
            }
        }

        throw new RouteNotFoundException('No route register for this' . $this->path);
    }

    public function addRoute($uri, $handler, array $methods = ['GET'])
    {
        $path = $this->parseUri($uri);
        $params = $this->parseParams($uri, $path);
        $this->uris[] = $uri;
        $this->routes[$path] = $handler;
        $this->methods[$path] = $methods;
        $this->params[$path] = $params;
    }

    protected function parseUri($uri)
    {
        $uri = explode('{', $uri);
        if ($uri[0] === '/') {
            return $uri[0];
        };
        return rtrim($uri[0], '/');
    }

    protected function parseParams($uri, $path)
    {
        $parsePath = str_replace($path, '', $uri);
        $paths = explode('{', $parsePath);
        unset($paths[0]);

        if (count($paths) < 1) {
            return;
        }

        $params = [];

        foreach ($paths as $param) {
            $params[] = str_replace(['/', '}'], '', $param);
        }

        $this->expectedParams[$path] = $params;
    }

    protected function setParams($uri, $path)
    {
        $path = str_replace($uri, '', $path);
        $path = explode('/', $path);
        unset($path[0]);

        $params = [];

        foreach ($path as $param) {
            $param = explode("=", $param);
            if (isset($param[1]) && !in_array($param[0], $this->expectedParams[$uri])) {
                continue;
            }

            $params[] = isset($param[1])
                ? trim(filter_var($param[1], FILTER_SANITIZE_SPECIAL_CHARS))
                : trim(filter_var($param[0], FILTER_SANITIZE_SPECIAL_CHARS));
        }

        $this->expectedParams[$uri] = isset($this->expectedParams[$uri]) ? $this->expectedParams[$uri] : [];

        if (count($params) !== count($this->expectedParams[$uri])) {
            throw new InvalidRequestParamsException('Invalid parameters name or quantity passed for ' . $uri);
        }

        $this->params[$uri] = $params;
    }

    public function isAPI()
    {
        $uri = explode('/', $this->path);
        if ($uri[1] === 'api') {
            return true;
        }
        return;
    }

    public function getResponse()
    {
        if (is_null($this->methods[$this->path])) {
            throw new RouteNotFoundException('No route register for this' . $this->path);
        }

        if (!in_array($_SERVER['REQUEST_METHOD'], $this->methods[$this->path])) {
            throw new MethodNotAllowedException;
        }

        return $this->routes[$this->path];
    }

    public function getParams()
    {
        if (is_null($this->expectedParams[$this->path])) {
            return [];
        }

        if (is_null($this->params[$this->path])) {
            throw new RouteNotFoundException('No route register for this' . $this->path);
        }

        return $this->params[$this->path];
    }

    public function getMethods()
    {
        return $this->methods[$this->path];
    }
}
