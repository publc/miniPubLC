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
            'token' => function () {
                return new Token;
            },
            'validate' => function () {
                return new Validate;
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
        // if ($this->validateToken !== true) {
        //     return;
        // }

        $validate = $this->container->validate;
        $validate->check($params, $fields);

        if (!$validate->passed()) {
            return $validate->errors();
        }

        return $validate->passed();
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

    protected function validateToken()
    {
        $token = $this->getToken();
        $tokenClass = $this->container->token;

        if (!$tokenClass->check($token)) {
            return;
        }
        return true;
    }

    protected function getToken()
    {
        if ($this->isAPIRequest() === true) {
            $params = $this->getAPIParams();
            return $params['token'];
        }

        if (!$this->exists()) {
            return;
        }

        return $this->get("token");
    }
}
