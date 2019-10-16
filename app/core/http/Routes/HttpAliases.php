<?php

namespace Http\Routes;

class HttpAliases
{
    
    const INDEX_PAGE = ROOT. "public/index.php";
    static private $aliases = [];
    static private $stdAliases = [];

    static function search($std=null, $prefix=null)
    {
        $indexpage = filemtime(self::INDEX_PAGE);

        $aliaeseTxt = __DIR__. '/paths/aliases.txt';
        $aliases = filemtime($aliaeseTxt);

            if(file_exists($aliaeseTxt) && ($indexpage  === $aliases)){

                $handle = fopen($aliaeseTxt, "r");
                if ($handle) {
                    $buffer = json_decode(fgets($handle));
                    foreach($buffer as $k => $v){
                        self::$aliases[$k] = $v;
                    }
                    fclose($handle);
                }
            }

        else {
            if(is_file(self::INDEX_PAGE)){
                $f = file(self::INDEX_PAGE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $l = explode("Path::",implode(' ', $f));

                foreach ($l as $k => $v)
                {
                    $a = self::detectAlias($v);
                    if($a) self::$aliases[self::clean($a)] = self::detectPath($v);
                }
                self::detectArgs(self::$aliases);
                $f=null;

                file_put_contents($aliaeseTxt, json_encode(self::$aliases));
                 touch($aliaeseTxt, $indexpage);
            }
        }

        if(is_object($std)) {
            $file = self::convertFuncInString($std);
            $l = explode("Path::",$file);

            foreach ($l as $k => $v) {
                $a = self::detectAlias($v);
                if($a) self::$stdAliases[self::clean($a)] = $prefix . self::detectPath($v);
            }
            $handle = fopen($aliaeseTxt, "r");
            $changeValues = 0;
            if ($handle) {
                $buffer = (array)json_decode(fgets($handle));

                foreach(self::$stdAliases as $k => $v){
                    if(!in_array($v, $buffer)){
                        ++$changeValues;
                        $buffer[$k] = $v;
                    }
                }
                fclose($handle);
                if($changeValues){
                    file_put_contents($aliaeseTxt, json_encode($buffer));
                    touch($aliaeseTxt, $indexpage);
                }
            }
        }
    }
    static function listAliases(){ return self::$aliases;}
    private static function detectAlias($string)
    {
        $name ='';

        $position = strpos($string, "alias('#");
        if($position !== false) {

            for($i=$position; $i < mb_strlen($string);$i++){
                    $name .= $string{$i};
            }
        }
        return $name ?: false;
    }
    private static function detectPath($string)
    {

        $start = strpos($string,'(') + 2;
        $finish = strpos($string,',');
        $t = $finish - $start;

        return trim(substr($string, $start, $t), "'\"");
    }
    private static function detectArgs(&$arr)
    {

        $url = [];

        foreach ($arr as $k => $v){

            if((strpos($v, '@') !== false) && (strpos($v, '[') !== false)){
                $min = min(\Druid::mb_stripos_all($v, '['));
                $max = max(\Druid::mb_stripos_all($v, ']'));
                $noFilters = self::deleteFilters($v, $min);
                $url[$k] = $noFilters;


            }else {
                $url[$k] = $v;
            }

        }
        return $arr = $url;
    }
    private static function deleteFilters($string, $start)
    {
        $length = mb_strlen($string) - ($start);
        return self::mb_substr_replace($string, '', $start, $length);
    }
    private static function clean($string)
    {
        $cl = "";
        for($i=0; $i < mb_strlen($string); $i++){
            if(ctype_alnum($string{$i}) || ($string{$i} == '#')){
                $cl .= $string{$i};
            }
        }
        return ltrim(ltrim($cl,'alias'));
    }
    private static function convertFuncInString($func)
    {
        $func = new \ReflectionFunction($func);
        $filename = $func->getFileName();
        $start_line = $func->getStartLine() - 1;
        $end_line = $func->getEndLine();
        $length = $end_line - $start_line;
        $source = file($filename);
        return  implode("", array_slice($source, $start_line, $length));
    }
   static function mb_substr_replace($string, $replacement, $start, $length) 
   {
        return mb_substr($string, 0, $start).$replacement.mb_substr($string, $start+$length);
    }
}