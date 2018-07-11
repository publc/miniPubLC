<?php

namespace App\Core;

use Exception;
use App\Core\Container;

class Request
{
    protected $container;

    protected $server = [
        "host" => "HTTP_HOST",
        "uri" => "REQUEST_URI",
        "method" => "REQUEST_METHOD",
        "connection" => "HTTP_CONNECTION",
        "user_agent" => "HTTP_USER_AGENT",
        "accept" => "HTTP_ACCEPT",
        "accept_encoding" => "HTTP_ACCEPT_ENCODING",
        "path" => "PATH",
        "server_name" => "SERVER_NAME",
        "address" => "SERVER_ADDR",
        "port" => "SERVER_PORT",
        "remote_address" => "REMOTE_ADDR",
        "root" => "DOCUMENT_ROOT",
        "scheme" => "REQUEST_SCHEME",
        "protocol" => "SERVER_PROTOCOL",
        "script_name" => "SCRIPT_NAME",
        "script_file" => "SCRIPT_FILENAME",
        "remote_port" => "REMOTE_PORT",
        "gateway" => "GATEWAY_INTERFACE",
        "query" => "QUERY_STRING",
        "time" => "REQUEST_TIME",
        "float_time" => "REQUEST_TIME_FLOAT"
    ];

    public function __construct()
    {
        $this->container = new Container([
            'config' => function () {
                return new Config;
            },
            'validate' => function () {
                return new Validate;
            },
            'jwt' => function () {
                return new JWT;
            }
        ]);
    }

    public function getServerParam($param)
    {
        if (is_string($param)) {
            return $_SERVER[$this->server[$param]];
        }
    }

    public function getServerParams()
    {
        $params = [];
        foreach ($this->server as $key => $param) {
            if (!isset($params[$key])) {
                $params[$key] = [];
            }
            $params[$key] = $_SERVER[$param];
        }
        return $params;
    }

    public function get($item, $method = "POST")
    {
        if ($method === "POST") {
            return isset($_POST[$item]) ? $_POST[$item] : "";
        }

        return isset($_GET[$item]) ? $_GET[$item] : "";
    }

    public function getParams()
    {

        if ($_SERVER[$this->server['method']] === "POST") {
            if (!is_null($this->getAPIParams())) {
                return $this->getAPIParams();
            }
            return $_POST;
        }

        if ($_SERVER[$this->server['method']] === "GET") {
            return $_GET;
        }

        return;
    }

    public function exists($type = "post")
    {
        switch ($type) {
            case 'post':
                return (!empty($_POST)) ? true : false;
                break;

            case 'get':
                return (!empty($_GET)) ? true : false;
                break;

            default:
                return false;
                break;
        }
    }

    public function getAPIParams()
    {
        if ($this->isAPIRequest() !== true) {
            return;
        }
        return json_decode(file_get_contents("php://input"));
    }

    public function isAPIRequest()
    {
        $header = $this->xmlHTTPRequest();
        if (empty($header) || strtolower($header) !== 'xmlhttprequest') {
            return;
        }
        return true;
    }

    public function validate($params, $fields = array())
    {
        $validate = $this->container->validate;
        $validate->check($params, $fields);

        if (!$validate->passed()) {
            return $validate->errors();
        }

        return $validate->passed();
    }

    public function validateToken()
    {
        $validate = $this->getTokenContent();

        if (is_object($validate) && !empty($validate->iat)) {
            return true;
        }

        return $validate;
    }

    public function getTokenContent()
    {
        $token = $this->getToken();

        if (is_null($token)) {
            return 'No token provided';
        }

        $jwt = $this->container->jwt;
        $config = $this->container->config;

        try {
            $payload = $jwt::decode($token, $config->get('jwt')->secret, ['HS256']);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $payload;
    }

    protected function getHeaders()
    {
        return !empty(getallheaders()) ? getallheaders() : null;
    }

    protected function xmlHTTPRequest()
    {
        $headers = $this->getHeaders();
        return isset($headers['HTTP_X_REQUESTED_WITH']) ? $headers['HTTP_X_REQUESTED_WITH'] : null;
    }

    protected function getToken()
    {
        if ($this->isAPIRequest() === true) {
            $headers = $this->getHeaders();
        }

        if (empty($headers['Authorization'])) {
            return;
        }

        $token = trim($headers['Authorization']);

        if (preg_match('/Bearer\s(\S+)/', $token, $match)) {
            return $match[1];
        }
    }
}
