<?php

class View
{
    private static $data;
    
    static function show($file, $data=null, $status=200)
    {
        self::$data = $data;
        if(is_array(self::$data) && !self::isAssoc(self::$data) ){
            if(self::keyIsObject(self::$data)){
                $debug = debug_backtrace();
                $filename = $debug[0]['file'];
                $line = $debug[0]['line'];
                $values = $debug[0]["args"][1];
                $args = self::getArgs($filename, $line);$data = self::createObject($args, $values);
            }
        }
        $file = self::getExtension($file) ?: $file.'.php';
         self::detectClone($file, $data, $status);
    }

    private static function getExtension($ext)
    {
        if(strrpos($ext, '.') !== false){
            $pos = strrpos($ext, '.');
            if(substr($ext, $pos) != '.php'){
                return $ext;
            }
        }
        else {
            return false;
        }

    }
    
    private static function detectClone($includeFile, $data, $status)
    {
        $clones = [];
        $file = '';
        $handle = fopen(PATHS .'file.txt', "r");

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if(strpos(strtolower($line), strtolower('/'.$includeFile)) !== false) {
                    $clones[]['file'] = explode("|", $line)[0];
                }
            }
            fclose($handle);
        }

        if(isset($clones)){

            if(count($clones) > 1) {
                echo "
                <style>
.error_clone {
display: inline-block;background:rgba(255, 0, 0, 0.68);position: absolute; z-index: 33;top: 0;right: 0;
font-family: 'Inconsolata', monospace;
font-size: 20px;line-height: 28px;
}
@keyframes scale-up-center {0% {
-webkit - transform: scale(0.5);
transform: scale(0.5);}100 % {-webkit - transform: scale(1);transform: scale(1);}}
.scale-up-center {
	        animation: scale-up-center 0.4s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
}
</style >
<div class='error_clone scale-up-center'>
                Error: You used " .count($clones). " times <strong>{$includeFile}</strong><br>";
                for($i=0; $i < count($clones); $i++)
                {
                    echo $clones[$i]['file']."<br>";
                }
                echo "</p></div><br>";
                echo "<script>
let error = document.querySelector('.error_clone');
error.remove();
document.body.appendChild(error); 
</script>";
                exit();
            }

            else {
                    $file = trim($clones[0]['file']);
                if(Druid::isDruid($file)){
                    if($status) { http_response_code($status); }
                    return (new Druid)->render($file, $data);
                }
                else {
                        if($status) { http_response_code($status); }
                        
                        require trim($file, PHP_EOL);
                }
            }
        }

        else {

            exit("<span style='color: red;'>Error</span> : file <strong>{$includeFile}</strong> unexist!");
        }
    }

    private static function keyIsObject(array $arr)
    {
        $obj = array_filter($arr, function($v) { return is_object($v) ?: false;});
        return (!$obj) ? (object)$arr : false;

    }

    private static function createObject(array $arr, array $keys)
    {
        $comb = array_combine($arr,$keys);
        $obj = new stdClass();
        foreach ($comb as $k => $v){
            $obj->$k = $v;
        }
        return self::$data = $obj;
    }

    private static function getArgs($file, $line){
        $arg = file($file)[$line-1];
        $ex = explode('[', $arg)[1];
        $ex = explode(',', substr($ex, 0, strpos($ex, ']')));
        $map = array_map(function($m){
            return ltrim(ltrim(trim($m), '$'), '@');
        }, $ex);
        return $map;
    }

    private static function isAssoc (array $array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}