<?php

namespace App\Core;

class Session
{
    public function get($name)
    {
        return ($this->exists($name)) ? $_SESSION[$name] : null;
    }

    public function put($name, $value)
    {
        return $_SESSION[$name] = $value;
    }

    public function exists($name)
    {
        return (isset($_SESSION[$name]) && !empty($_SESSION[$name])) ? true : false;
    }

    public function delete($name)
    {
        if ($this->exists($name)) {
            unset($_SESSION[$name]);
        }
    }

    public function flash($name, $content = '')
    {

        if ($this->exists($name)) {
            $session = $this->get($name);
            $this->delete($name);
            return $session;
        }
        $this->put($name, $content);
    }
}
