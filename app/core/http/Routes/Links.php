<?php

class Links
{
    static $list;
    static function alias($alias)
    {
        if(!self::$list){
            self::$list = Http\Routes\HttpAliases::listAliases();
        }
        echo array_key_exists($alias, self::$list) ? self::$list[$alias] : 'Incorrect Alias';
    }
}