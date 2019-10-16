<?php

namespace Http\Routes;

class Route {

    static public $page, $url, $urlPaginator, $firstPattern;
    static protected $path = [], $args =[], $prefix = '', $params;
    static private $count;

    static function get($pattern, $action, $params = null)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            self::execute($pattern, $action, $params) ? exit : '';
        }

    }
    static function post($pattern, $action, $params = null)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && self::unexistRequest('PUT', 'DELETE'))
            self::execute($pattern, $action, $params)? exit : '';
    }
    static function put($pattern, $action, $params = null)
    {
        $post = array_change_key_case($_POST, CASE_UPPER);
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['PUT']))
            self::execute($pattern, $action, $params)? exit : '';
    }
    static function delete($pattern, $action, $params = null)
    {
        $post = array_change_key_case($_POST, CASE_UPPER);
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['DELETE']))
            self::execute($pattern, $action, $params)? exit : '';

    }
    static function both($pattern, $action, $params = null)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST')
            self::execute($pattern, $action, $params)? exit : '';;

    }
    static function similar($pattern, $action, $params = null)
    {
        self::$firstPattern = \Druid::mb_stripos_all($pattern[0][0], '@');
        $count = count($pattern[0]);

        if(!$action){
            $action = array_fill(0, $count, 'get');
        }

        for($i=0; $i < $count; $i++){
            if(self::{$pattern[1][$i]}($pattern[0][$i], $action, $params)) return;
        }

    }
    static function prefix($prefix, callable $function) {
        HttpAliases::search($function, "$prefix/");
        self::$prefix = "$prefix/";
        $function(self::$prefix);
        self::$prefix='';

    }

    private static function cutGetParams($get)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST'){
            return (strpos($get[0], '?') === false) ? $get : array_fill(0,1,explode('?', $get[0])[0]);
        }
        return $get;
    }
    private static function checkerPattern($pattern)
    {
        $pattern = explode('/', trim($pattern,'/'));
        $u = explode('/', trim($_SERVER['REQUEST_URI'],'/'));

        $u = self::cutGetParams($u);

        $uri = array_map(function($u) { return rawurldecode($u);}, $u);
        if(count($uri) == count($pattern))
        {
            for($i=0; count($uri)>$i; $i++){
                if($pattern[$i] === $uri[$i])
                {
                    self::$path [] = $uri[$i];
                }
                elseif (self::nullOrNot($pattern[$i], $uri[$i])){
                    self::$path [] = $uri[$i];
                    self::$args[] = $uri[$i];
                }
                elseif (self::nullOrNot($pattern[$i], $uri[$i]) === false){
                    self::$path = null;
                    return false;
                }
            }
            return self::$path;
        }
        else { return false;}

    }
    private static function callFunction($func)
    {
        $all = [];

        $function = new \ReflectionFunction($func);
        if($function->getNumberOfParameters())
        {
            foreach ($function->getParameters() as $param)
            {

                if($param->isOptional())
                {
                    $all[$param->getName()] = $param->getDefaultValue();

                }
                elseif(array_key_exists($param->getName(), self::$args)) {
                    $all[$param->getName()] = self::$args[$param->getName()];
                }
            }

        }
        return self::$args ? call_user_func_array($func, $all) : $func();
    }
    private static function callMethod($obj){
        list($class, $method) = explode('->', $obj);
        if(!$method) {$method = '__construct';}
        $invoke = new \ReflectionMethod($class, $method);
        self::$args = (array)self::$args;
        foreach (self::$args as $k => $v){
            if($v{0} == '@'){
                self::$args[$k] = self::$page[ltrim($v, '@')];
            }
        }
        return $invoke->invokeArgs((new \ReflectionClass($class))->newInstanceWithoutConstructor(), self::$args);
    }
    private static function nullOrNot($symbol, $value)
    {
        $pattern = '#^@+#uix';
        $value = rawurldecode($value);

        if(preg_match_all($pattern, $symbol) && (!is_null($value)))
        {
            $key = self::detectFilterInArgs(trim($symbol, '@'));
            self::$page[$key] = $value;

            $filtered ='';

            for($i=0; $i < strlen($key); $i++) {

                if(ctype_alpha($key{$i}))
                {
                    $filtered .= $key{$i};
                }
                else { break;}
            }
            if(strpos($symbol, '[') !== false){

                $setFunctions = trim(trim($symbol,'@'. $filtered), '[]');

                $method = self::parseFunction($setFunctions);

                if(isset($method['arg'])){
                    $args = explode(',', $method['arg']);

                    $args = array_map(function($a){ return trim($a); }, $args);

                    $value = forward_static_call_array(['Filters', $method['name']],[$value, $args]);
                    return $value;

                }
                else {
                    $value = forward_static_call_array(['Filters', $method['name']],[$value]);
                    return $value;
                }
                return self::$args[$filtered] = $value;
            }
            else {
                self::$args[$key] = $value;
                return true;
            }
        }
        else {
            self::$page[0] = [];
            return false; }
    }
    private static function execute($pattern, $action, $params){

        $pattern = self::$prefix.$pattern;

        if(self::checkerPattern($pattern))
        {
            self::nameRoute($pattern);

            $params ? (self::$args = $params) : self::$args;


            if(is_callable($action))
            {
                ++self::$count;
                self::callFunction($action);
            }

            elseif (is_string($action)){
                ++self::$count;
                self::callMethod($action);
            }
            else { throw new \PathException("Second argument in Route must be  'class->method' or anonymous function only!" );}
            ++self::$count;

            return true;

        }
        else { return false;}
    }
    static function parseFunction($function)
    {
        if(!ctype_alnum($function)){
            for($i=0; $i < strlen($function); $i++){
                if($function{$i} == '(' ){
                    $start = $i;
                }
                elseif ($function{$i} == ')'){
                    $end = $i;
                }
            }
            $parse['name'] = substr($function, 0, $start);
            $parse['arg'] = trim(substr($function, $start+1 ),')');
        }
        else {
            $parse['name'] = $function;
        }
        return $parse;
    }

    private static function detectFilterInArgs($arg)
    {
        if(strpos($arg, '[') !== false){
            $end = strpos($arg, '[');
            return substr($arg, 0, $end);
        }
        else {
            return $arg;
        }
    }

    private static function nameRoute($pattern) {

        $symbol = \Druid::mb_stripos_all($pattern, '@');
        if($symbol){
            $count = count($symbol);
            $explodeURI = explode('/', ltrim(URI, '/'));
            $implode = implode('/', $explodeURI);
            $end = end($explodeURI);
            $typeDigit = ctype_digit($end);

            if($count > 1){
                $at = $explodeURI;
                array_pop($at);
                $at = implode('/', $at);
                if(!$typeDigit) {

                    $at .= '/'. $end;
                }
            }
            else {
                $at = implode('/', $explodeURI);
                if(!$typeDigit) {
                    $at = $implode;
                }
                else {
                    if(array_search($end, $explodeURI) == (count($explodeURI)-1)) {
                        if(self::$firstPattern === false) {
                            array_pop($explodeURI);
                            $at = implode('/', $explodeURI);
                        }
                        else {
                            array_pop($explodeURI);
                            $at = implode('/', $explodeURI);
                        }
                    }

                    else {
                        $at = $end;
                    }
                }
            }
            self::$url = URL;

            self::$urlPaginator = HTTP . HOST . '/' . $at;
        }
        else {
            self::$url = URL;
            self::$urlPaginator = URL; }
    }

    private static function unexistRequest(...$requests) 
    {
        $post = array_change_key_case($_POST, CASE_UPPER);
        foreach ($requests as $r){
            if(array_key_exists($r, $post)){
                return false;
            }
        }
        return true;
    }

    static function unexistPath()
    {
        if($_SERVER['REQUEST_URI'] == '/error'){

         return \View::show(ERROR_PAGE, null, 404);
        }
        elseif (!static::$count) {
            return \View::show(ERROR_PAGE, null, 404);
        }
    }
    
    function __call($name, $arguments)
    {
        exit;
    }

}