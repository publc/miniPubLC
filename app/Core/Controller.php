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

    protected function get()
    {
        $model = $this->getModel();
        return $model->get();
    }

    protected function find($field, $value)
    {
        $model = $this->getModel();
        return $model->find($field, $value);
    }

    protected function create($params = array())
    {
        $model = $this->getModel();
        return $model->create($params);
    }

    protected function update($filter, $params = array())
    {
        $model = $this->getModel();
        return $model->patch($filter, $params);
    }

    protected function delete($params = array())
    {
        $model = $this->getModel();
        return $model->destroy($params);
    }

    protected function getModel($model = null)
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
