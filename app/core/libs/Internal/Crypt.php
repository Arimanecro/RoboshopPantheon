<?php


class Crypt 
{

    static function encode($string,$key) 
    {
        $key = sha1($key);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        $hash = '';
        $j = 0;
        for ($i = 0; $i < $strLen; $i++) {
            $ordStr = ord(substr($string,$i,1));
            if ($j == $keyLen) { $j = 0; }
            $ordKey = ord(substr($key,$j,1));
            $j++;
            $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
        }
        return $hash;
    }
    static function decode($string,$key) 
    {
        $key = sha1($key);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        $hash = '';
        $j = 0;
        for ($i = 0; $i < $strLen; $i+=2) {
            $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
            if ($i == $keyLen) { $j = 0; }
            $ordKey = ord(substr($key,$j,1));
            $j++;
            $hash .= chr($ordStr - $ordKey);
        }
        return $hash;
    }

}