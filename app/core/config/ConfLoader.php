<?php

namespace Config;
include __DIR__ . '/../libs/Traits/Scanner.php';

class ConfLoader
{

    use \app\core\Libs\Traits\Scanner;

    private static $primaryConf = '/app/core/config/Framework';
    private static $userConf = '/app/core/config/Developer';

    static function setup()
    {
        
        self::confPrimary(self::$primaryConf);
        self::confUser(self::$userConf);

        return;
    }

    static private function confPrimary($path)
    {
        return self::tree($path);
    }

    static private function confUser($path)
    {
        return self::tree($path);
    }
}