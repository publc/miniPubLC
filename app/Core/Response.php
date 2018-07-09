<?php

namespace App\Core;

class Response
{
    protected $body;

    protected $statusCode = 200;

    protected $headers = [];

    protected $view;

    protected $layout;

    protected $data = [];

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withStatus($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withJSON($body)
    {
        $this->withHeader("Content-Type", "application/json");

        $this->body = json_encode($body);
        return $this;
    }

    public function withHeader($name, $value)
    {
        $this->headers[] = [$name, $value];
        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function view($view, $data = array(), $layout = null)
    {
        $this->view = $view;
        $this->data = $data;
        $this->layout = $layout;
        return $this;
    }

    public function layout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    public function with($data = array())
    {
        $this->data = $data;
        return $this;
    }

    public function getView()
    {
        return $this->view;
    }

    public function loadView()
    {
        $data = $this->data;
        if (!$this->layout) {
            require_once '../source/views/' . $this->view . '.view.php';
            return;
        }

        $view = '../source/views/' . $this->view . '.view.php';
        require_once '../source/views/layouts/' . $this->layout . '.layout.php';
    }
}
