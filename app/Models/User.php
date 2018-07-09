<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = "users";

    public function get()
    {
        return parent::get();
    }

    public function find($value)
    {
        $params['db_method'] = 'single';
        $params['filters'] = [
            'filter' => 'email',
            'op' => '=',
            'value' => $value
        ];
        parent::setRequestParams($params);
        return parent::findOne();
    }

    public function create($params = array())
    {
        $params = [
            "params" => $params
        ];

        parent::setRequestParams($params);
        return parent::create();
    }

    public function update($params = array())
    {
        parent::setRequestParams($params);
        return parent::update();
    }
}
