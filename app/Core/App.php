<?php

namespace App\Core;

use App\Core\Router;
use App\Core\Response;
use App\Core\Container;
use App\Core\Exceptions\RouteNotFoundException;

class App
{
    protected $container;

    public function __construct()
    {
        $this->container = new Container([
            'router' => function () {
                return new Router;
            },
            'response' => function () {
                return new Response;
            }
        ]);
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function get($uri, $handler)
    {
        $this->container->router->addRoute($uri, $handler, ['GET']);
    }

    public function post($uri, $handler)
    {
        $this->container->router->addRoute($uri, $handler, ['POST']);
    }

    public function put($uri, $handler)
    {
        $this->container->router->addRoute($uri, $handler, ['PUT']);
    }

    public function patch($uri, $handler)
    {
        $this->container->router->addRoute($uri, $handler, ['PATCH']);
    }

    public function delete($uri, $handler)
    {
        $this->container->router->addRoute($uri, $handler, ['DELETE']);
    }

    public function map($uri, $handler, array $methods = ['GET'])
    {
        $this->container->router->addRoute($uri, $handler, $methods);
    }

    public function run()
    {
        $router = $this->container->router;
        $router->setPath($_SERVER['REQUEST_URI'] ?? '/');

        try {
            $response = $router->getResponse();
            $params = $router->getParams();
        } catch (RouteNotFoundException $e) {
            if ($this->container->has('errorHandler')) {
                $response = $this->container->errorHandler;
            } else {
                return;
            }
        }

        $this->dispatch($router, $response, $params);
    }

    protected function dispatch($router, $callable, $params)
    {
        if ($router->isAPI() !== true) {
            return $this->respond($this->proccess($callable, $params));
        }
        $this->dispatchAPI($callable, $params);
    }

    protected function dispatchAPI($callable, $params)
    {
        $response = $this->container->response;
        $headers = getallheaders();
        $apiHeader = isset($headers['HTTP_X_REQUESTED_WITH']) ? $headers['HTTP_X_REQUESTED_WITH'] : null;
        if (empty($apiHeader) || strtolower($apiHeader) !== 'xmlhttprequest') {
            return $this->respond($response->withStatus(404));
        }
        return $this->respond($this->proccessAPI($callable, $params));
    }

    protected function proccessAPI($callable, $params)
    {
        $response = $this->container->response;

        if (is_string($callable)) {
            $callable = '\App\Api\\' . $callable;
            $call = new $callable;
        }

        $request = json_decode(file_get_contents("php://input"));
        $method = $this->requestMethod($request);
        if (is_null($method)) {
            return $response->withStatus(400)->withJSON('Method Not allowed');
        }
        $callable = [$call, $method];

        if (is_array($callable)) {
            array_unshift($params, $request, $response);
            return call_user_func_array($callable, $params);
        }

        return $callable($response);
    }

    protected function requestMethod($request)
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($method === 'get') {
            return $method;
        }

        if ($method === 'post') {
            $reqMethod = strtolower($data->method);
            if (!empty($data->method) &&
                ($reqMethod === 'put' || $reqMethod === 'patch' || $reqMethod === 'delete')) {
                return $reqMethod;
            }
            return $method;
        }

        return;
    }

    protected function proccess($callable, $params)
    {
        $response = $this->container->response;

        if (is_string($callable)) {
            $callable = explode('@', $callable);
            $callable[0] = '\App\Controllers\\' . $callable[0];
            $callable[0] = new $callable[0];
        }

        if (is_array($callable)) {
            array_unshift($params, $response);
            return call_user_func_array($callable, $params);
        }

        return $callable($response);
    }

    public function respond($response)
    {
        if (!$response instanceof Response) {
            echo $response;
            return;
        }

        header(sprintf(
            '%s %s %s',
            $_SERVER["SERVER_PROTOCOL"],
            $response->getStatusCode(),
            ''
        ));

        foreach ($response->getHeaders() as $header) {
            $header = implode(": ", $header);
            header($header);
        }

        if ($response->getView()) {
            $response->loadView();
            return;
        }

        echo $response->getBody();
    }
}
