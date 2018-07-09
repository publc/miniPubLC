<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    // protected $table = "users";

    public function get()
    {
        return parent::get();
    }

    public function find($value)
    {
        $params['filters'] = [
            'filter' => 'username',
            'op' => '=',
            'value' => $value
        ];
        parent::setRequestParams($params);
        return parent::get();
    }

    public function create($params = array())
    {
        $params = [
            "params" => $params
        ];

        parent::setRequestParams($params);
        return parent::create();
    }
}
