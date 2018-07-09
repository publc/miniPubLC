<?php

namespace App\Core\Storage;

use App\Core\Storage\Contracts\StorageInterface;

class SessionStorage implements StorageInterface
{
    protected $storageKey = 'items';

    public function __construct($storageKey = null)
    {
        if ($storageKey) {
            $this->storageKey = $storageKey;
        }

        if (!isset($_SESSION[$this->storageKey])) {
            $_SESSION[$this->storageKey] = [];
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$this->storageKey][$key] = serialize($value);
    }

    public function get($key)
    {
        if (!isset($_SESSION[$this->storageKey][$key])) {
            return;
        }
        return unserialize($_SESSION[$this->storageKey][$key]);
    }

    public function delete($key)
    {
        unset($_SESSION[$this->storageKey][$key]);
    }

    public function destroy()
    {
        unset($_SESSION[$this->storageKey]);
    }

    public function all()
    {
        $items = [];
        foreach ($_SESSION[$this->storageKey] as $key => $item) {
            if (!isset($items[$key])) {
                $items[$key] = [];
            }
            $items[$key] = unserialize($item);
        }
        return $items;
    }
}
