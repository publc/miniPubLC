<?php

namespace App\Core;

use App\Core\Database;
use App\Core\Auth\User;

class Validate
{

    protected $passed = false;

    protected $errors = array();

    public function check($source, $items = array())
    {
        foreach ($items as $item => $rules) {
            $value = $source[$item];
            $this->proccess($source, $item, $rules, $value);
        }

        if (empty($this->errors)) {
            $this->passed = true;
        }

        return $this;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function passed()
    {
        return $this->passed;
    }

    protected function proccess($source, $item, $rules, $value)
    {
        foreach ($rules as $rule => $ruleValue) {
            switch ($rule) {
                case 'required':
                    $this->required($value, $item);
                    break;
                case 'min':
                    $this->min($value, $ruleValue, $item);
                    break;
                case 'max':
                    $this->max($value, $ruleValue, $item);
                    break;
                case 'matches':
                    $this->matches($value, $source, $ruleValue, $item);
                    break;
                case 'unique':
                    $this->unique($value, $ruleValue, $item);
                    break;
                case 'email':
                    $this->email($value, $item);
                    break;
            }
        }
    }

    protected function required($value, $item)
    {
        if (empty($value)) {
            $this->addError("{$item} is required");
            return;
        }
        return true;
    }

    protected function min($value, $ruleValue, $item)
    {
        if (strlen($value) < $ruleValue) {
            $this->addError("{$item} must be a minimun of {$ruleValue} characters");
        }
        return;
    }

    protected function max($value, $ruleValue, $item)
    {
        if (strlen($value) > $ruleValue) {
            $this->addError("{$item} must be a maximum of {$ruleValue} characters");
        }
        return;
    }

    protected function matches($value, $source, $ruleValue, $item)
    {
        if ($value != $source[$ruleValue]) {
            $this->addError("{$item} must match {$ruleValue}");
        }
        return;
    }

    protected function unique($value, $ruleValue, $item)
    {
        $model = "\App\Models\\" . ucwords($value);
        $model = new $model;
        if (!class_exists($model)) {
            $this->addError("No model for this validation");
        }

        $check = $model->check($item, $value);
        if ($check > 0) {
            $this->addError("{$item} already exists");
        }
        return;
    }

    protected function email($value, $item)
    {
        $email = filter_var($value, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError("Invalid {$item}");
        }
        return;
    }

    protected function addError($error)
    {
        $this->errors[] = $error;
    }
}
