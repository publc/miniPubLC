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

    public function find($field, $value)
    {
        $params['db_method'] = 'single';
        $params['filters'] = [
            'filter' => $field,
            'op' => '=',
            'value' => $value
        ];
        parent::setRequestParams($params);
        return parent::findOne();
    }

    public function check($item, $value)
    {
        $params['db_method'] = 'single';
        $params['filters'] = [
            'filter' => $item,
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

    public function patch($filter, $params = array())
    {
        $params = [
            "params" => $params
        ];
        $params['filters'] = [
            'filter' => 'email',
            'op' => '=',
            'value' => $filter
        ];

        parent::setRequestParams($params);
        return parent::update();
    }

    public function destroy($value)
    {
        $params['filters'] = [
            'filter' => 'email',
            'op' => '=',
            'value' => $value
        ];
        parent::setRequestParams($params);
        return parent::delete();
    }
}
