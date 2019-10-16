<?php
namespace Http\Routes;
class History
{
    static function back()
    {
        return $_SERVER['HTTP_REFERER'];
    }
}