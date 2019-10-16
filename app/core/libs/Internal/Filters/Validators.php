<?php

class Validators extends Filters 
{
    
    public static $errors =[];
    static protected $showError;
    private $value, $arr;

    function __construct($arr=null)
    {
        $this->arr = $arr ?: $_POST;
    }

    function filter(array $items)
    {

        foreach ($items as $k => $v){

            $field = ucfirst($k);

            $method = Path::parseFunction($v);

            if(isset($method['arg'])){
                $args = explode(',', $method['arg']);

                $args = array_map(function($a){ return trim($a); }, $args);

                $this->value = forward_static_call_array([self::class, $method['name']],[$this->arr[$k], $args]);
            }
            else {
                $this->value = forward_static_call_array([self::class, $method['name']],[$this->arr[$k]/*, $args*/]);

            }

            if($this->value === false){
                if(!empty(self::$msg[$method['name']])){

                    foreach (self::$msg[$method['name']] as $val){
                        static::$errors[] = '<strong>' . $field .'</strong>' . " consist errors : <br>" . $val . "<br>";
                    }
                    Filters::$msg =[];
                }
            }
        }
        return self::$msg ? false : $this->arr;
    }

    function returnData()
    {
        return $this->arr;
    }

    function returnJSON()
    {
        return json_encode($this->arr);
    }

    static function errors()
    {
        return self::$errors ?: false;
    }
}