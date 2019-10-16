<?php

require_once __DIR__ .'/../config/ConfLoader.php';
require_once __DIR__ .'/IdentifierSameClasses.php';
require_once __DIR__ .'/Naos/Gods.php';

final class Pantheon implements Bootstrap\Naos\Gods
{
    
    public $classes = [];
    
    function __construct()
    {
        foreach(get_class_methods(self::class) as $method){
            if($method !== '__construct'){
                call_user_func([self::class, $method]);
            }
        }
    }
    
    function Maat()
    {
        Config\ConfLoader::setup();
    }
    
    function Anubis()
    {
        (new bootstrap\IdentifierSameClasses);
        (new Path(new Http\Routes\Route()));

    }
    
    function Menhit()
    {
        Security\Http::sanitizer();
    }
    
    function Toth()
    {
        Http\Routes\HttpAliases::search();
    }
}

(new Pantheon());