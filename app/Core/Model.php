<?php

namespace App\Core;

use App\Core\App;
use App\Core\Container;

class Model
{
    private $container;

    protected $db;

    protected $table;

    private $stmt;

    private $fields;

    private $params = [];

    private $filters = [];

    private $joins = [];

    private $order = [];

    private $limit;

    private $offset;

    public function __construct()
    {
        $table = explode("\\", get_class($this));
        $this->table = !is_null($this->table) ? $this->table : strtolower($table[count($table) - 1] . 's');

        $this->container = new Container([
            'db' => function () {
                return new Database;
            }
        ]);

        $this->db = $this->container->db;
    }

    protected function get()
    {
        $fields = !empty($this->fields) ? $this->fields : '*';
        $this->stmt = "SELECT $fields FROM $this->table t";
        $this->prepareJoins();
        $this->prepareFilters();
        $this->prepareOrders();
        $this->prepareLimitOffset();
        $this->db->query($this->stmt);
        $this->bindParams();
        $this->bindFilters();
        $this->cleanRequestParams();
        return $this->db->resultSet();
    }

    protected function create()
    {
        $this->stmt = "INSERT INTO $this->table ";
        $this->prepareCreateParams();
        $this->stmt .= ' VALUES ';
        $this->prepareCreateParams(true);
        $this->db->query($this->stmt);
        $this->bindParams();
        $this->cleanRequestParams();
        return $this->db->execute();
    }

    protected function update()
    {
        $stmt = "UPDATE table_name
                SET column1 = value1, column2 = value2, ...
                WHERE condition;";
        $this->db->query($this->stmt);
        return $this->db->excecute();
    }

    protected function delete()
    {
        $stmt = "DELETE FROM table_name
                WHERE condition;";
        $this->db->query($this->stmt);
        return $this->db->excecute();
    }

    protected function setRequestParams($params)
    {
        $this->fields = isset($params['fields']) ? $params['fields'] : null;
        $this->params = isset($params['params']) ? $params['params'] : [];
        $this->filters = isset($params['filters']) ? $params['filters'] : [];
        $this->joins = isset($params['joins']) ? $params['joins'] : [];
        $this->order = isset($params['order']) ? $params['order'] : [];
        $this->limit = isset($params['limit']) ? $params['limit'] : null;
        $this->offset = isset($params['offset']) ? $params['offset'] : null;
    }

    protected function cleanRequestParams()
    {
        $this->fields = null;
        $this->params = [];
        $this->filters = [];
        $this->joins = [];
        $this->order = [];
        $this->limit = null;
        $this->offset = null;
    }

    private function prepareCreateParams($bind = false)
    {
        if (empty($this->params)) {
            return;
        }

        $this->stmt .= '(';
        foreach ($this->params as $key => $value) {
            if (!$bind) {
                $this->stmt .= '`' . $key . '`, ';
                continue;
            }
            $this->stmt .= ':' . $key . ', ';
        }

        $this->stmt = rtrim($this->stmt, ', ');
        $this->stmt .= ')';
    }

    private function prepareFilters()
    {
        if (count($this->filters) === 3) {
            $this->stmt .= ' WHERE t.' .
                        $this->filters['filter'] . ' ' .
                        $this->filters['op'] . ' :' .
                        $this->filters['filter'];
        }
    }

    private function prepareOrders()
    {
        if (count($this->order) === 2) {
            $this->stmt .= ' ORDER BY t.' . $this->order['param'] . ' ' . $this->order['order'];
        }
    }

    private function prepareLimitOffset()
    {
        if (!empty($this->limit)) {
            $this->stmt .= ' LIMIT ' . $this->limit;
        }

        if (!empty($this->offset)) {
            $this->stmt .= ' OFFSET ' . $this->offset;
        }
    }

    private function prepareJoins()
    {
        if (!empty($this->joins)) {
            $this->stmt .= ' INNER JOIN ' .
                            $this->joins['table'] . ' ' . $this->joins['as'] .
                            ' ON t.' . $this->joins['from'] . ' = ' . $this->joins['as'] . '.' . $this->joins['to'];
        }
    }

    private function bindParams()
    {
        if (empty($this->params)) {
            return;
        }

        foreach ($this->params as $key => $param) {
            $bind = ':' . $key;
            $value = $param;
            $this->db->bind($bind, $value);
        }
    }

    private function bindFilters()
    {
        if (count($this->filters) === 3) {
            $bind = ':' . $this->filters['filter'];
            $value = $this->filters['value'];
            $this->db->bind($bind, $value);
        }
    }
}
