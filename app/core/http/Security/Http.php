<?php
namespace Security;

class Http {

    static function sanitizer()
    {
        $methods = &$GLOBALS;

        foreach(HEALED_HTTP_REQUESTS as $v) {
            foreach($methods[$v] as &$v1) {
                if(is_array($v1)) {
                    $v1 = array_map('self::purification', $v1);
                }
                else {
                    self::purification($v1);
                }
            }
        }
        return $methods;
    }

    static function purification(&$v) 
    {
        $pattern = '/^-[0-9-+]+$/';
        if(preg_match($pattern, $v)) {
            $v = abs((int)$v);
        }
        else {
            $v = htmlentities($v, ENT_QUOTES | ENT_HTML5, "UTF-8");
        }
        return $v;
    }

    static function reverseSanitizer($string, $func = 'e') 
    {
        if($func == 'd')
            return self::purification($string);
        elseif($func == 'e')
            return html_entity_decode($string, ENT_QUOTES | ENT_HTML5, "UTF-8");
    }
    
}