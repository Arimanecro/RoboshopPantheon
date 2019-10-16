<?php

class Redirect
{
    static function to($path=null, $args = null)
    {
        if(!$path) { $path = \Http\Routes\Route::$url; }
        $alias = Http\Routes\HttpAliases::listAliases();
        if(array_key_exists($path, $alias)){

            if($args){
                foreach ($args as $k => $v){
                    $alias[$path] = str_replace($k, $v, $alias[$path]);
                }
            }
            header("Location: " . HTTP . HOST . "/" . $alias[$path]);
        }
        else {
            if(ctype_digit($path) || is_int($path)){
                $_SESSION['status'] = $path;
                header("Location: " . HTTP . HOST . '/error');
            }
            else { header("Location: " . $path);} }
            
    }
}