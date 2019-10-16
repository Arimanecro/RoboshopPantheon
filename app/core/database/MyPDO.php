<?php

class MyPDO 
{

    protected static $instance = null;

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function cxn() 
    {
        if(CONNECTION){
            if(self::$instance == null) {
                self::$instance = new PDO( DB_HOST.'; dbname='.DB_NAME , DB_USER, DB_PASS,
                    [
                        PDO::ATTR_CASE => PDO::CASE_NATURAL,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_EMULATE_PREPARES   => true,
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                        PDO::MYSQL_ATTR_FOUND_ROWS   => true,
                    ]);
            }
            return self::$instance;
        }

    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::cxn(), $method), $args);
    }

}