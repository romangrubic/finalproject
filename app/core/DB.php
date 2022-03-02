<?php

class DB extends PDO
{
    private static $instance=null;

    private function __construct($database)
    {
        $dsn='mysql:host=' . $database['server'] . ';dbname=' . $database['database'] . ';charset=utf8mb4';
        parent::__construct($dsn, $database['user'], $database['password']);

        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    public static function getInstance()
    {
        if(self::$instance==null){
            self::$instance = new self(App::config('database'));
        }
        return self::$instance;
    }
}