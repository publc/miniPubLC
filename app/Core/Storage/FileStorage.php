<?php

namespace App\Core\Storage;

use App\Core\Storage\Contracts\StorageInterface;

class FileStorage implements StorageInterface
{
    protected $storageKey = 'items';

    protected $path;

    protected $items = [];

    public function __construct($storageKey = null)
    {
        if ($storageKey) {
            $this->storageKey = $storageKey;
        }

        $this->path = '../storage/' . $this->storageKey;

        if (!file_exists($this->path)) {
            mkdir($this->path);
        }
    }

    public function set($key, $value)
    {
        $path = $this->path . '/' . $key;
        file_put_contents($path, $value);
    }

    public function get($key)
    {
        if ($this->keyExists($key)) {
            return file_get_contents($this->filePath($key));
        }
    }

    public function delete($key)
    {
        if (!$this->keyExists($key)) {
            return;
        }

        unlink($this->filePath($key));
    }

    public function destroy()
    {
        $this->process('unlinkFiles');
    }

    public function all()
    {
        $this->process('fillItems');
        return $this->items;
    }

    protected function unlinkFiles($item)
    {
        unlink($this->path . '/' . $item);
        return;
    }

    protected function fillItems($item)
    {
        $this->items[$item] = file_get_contents($this->filePath($item));
        return;
    }

    protected function process($callback, ...$params)
    {
        $dir = opendir($this->path);
        while (false !== ($item = readdir($dir))) {
            if (!in_array($item, ['.', '..'])) {
                $this->{$callback}($item);
            }
        }
    }

    protected function keyExists($key)
    {
        return file_exists($this->filePath($key));
    }

    protected function filePath($key)
    {
        return $this->path . '/' . $key;
    }
}
