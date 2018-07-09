<?php

namespace App\Core;

class Controller
{
    protected $model;

    protected $view;

    protected $modelClass;

    public function __construct()
    {
        if (empty($this->model)) {
            $model = explode("\\", get_class($this));
            $this->model = ucwords(str_replace("Controller", "", $model[count($model) - 1]));
        }

        $model = "\App\Models\\" . ucwords($this->model);
        if (class_exists($model)) {
            $this->modelClass = new $model;
        }
    }

    public function get()
    {
        return $this->modelClass->get();
    }

    public function create()
    {
        return $this->modelClass->create();
    }

    public function update()
    {
        return $this->modelClass->update();
    }

    public function delete()
    {
        return $this->modelClass->delete();
    }

    public function getModel($model = null)
    {
        if (is_null($model)) {
            $model = $this->model;
        }

        $model = "\App\Models\\" . ucwords($model);
        if (class_exists($model)) {
            return new $model;
        }
    }
}
