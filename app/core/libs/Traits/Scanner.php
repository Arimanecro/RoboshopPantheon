<?php

namespace app\core\Libs\Traits;

trait Scanner
{

    static function tree($dir=null)
    {
        $twigs = [];

        $dir = $_SERVER['DOCUMENT_ROOT'] . $dir;

        foreach (new \RecursiveIteratorIterator(
                     new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                     \RecursiveIteratorIterator::SELF_FIRST) as $f)
        {
            $f->isDir() ?:
                $twigs[] = "{$f->getPathname()}";
        }

        foreach ($twigs as $twig){
            require_once $twig;
        }
    }
}