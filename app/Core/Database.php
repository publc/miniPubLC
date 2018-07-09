<?php

namespace App\Core;

use PDO;
use App\Core\Container;

class Database
{
    protected $container;

    private $dbh;

    private $stmt;

    private $error;

    public function __construct()
    {
        $this->container = new Container([
            'config' => function () {
                return new Config;
            }
        ]);
        $conf = $this->container->config->get('db');
        $dsn = $conf->driver . ":host=" . $conf->host . ";dbname=" . $conf->name;
        $options = (array) $conf->options;
        try {
            $this->dbh = new PDO($dsn, $conf->user, $conf->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return $this->error;
        }
    }

    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute()
    {
        return $this->stmt->execute();
    }

    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
}
