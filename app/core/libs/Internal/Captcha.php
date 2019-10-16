<?php

class Captcha 
{

    private static $random = "3fdecef7fca6cf4c490d6f5b48b9ff0f";
    static $session_value;

    static private function crypt()
    {
        if(!file_exists(CAPTCHA_IMG . 'crypt')){

            if(isset($_SESSION['path'])) { $_SESSION['path'] = null;}
            
            $images = array_diff(scandir(CAPTCHA_IMG), array('..', '.'));
            if($images){

                file_put_contents(CAPTCHA_IMG. 'crypt', '');

                foreach ($images as $i){
                    $mime= mime_content_type(CAPTCHA_IMG . $i);
                    $explode = explode('.', $i);
                    $ext = end($explode);
                    if((strpos($mime, 'image') !== false)){
                        $n = Crypt::encode($i, self::$random) . ".$ext";
                        $newName =  CAPTCHA_IMG . $n;
                        $path = str_replace('\\', '/', ltrim(CAPTCHA_IMG, $_SERVER['DOCUMENT_ROOT']));

                        $sessionPath = HTTP . HOST . $path. $n;

                        file_put_contents(CAPTCHA_IMG . 'list', $sessionPath . PHP_EOL, FILE_APPEND);
                        copy(CAPTCHA_IMG. $i, $newName);
                        unlink(CAPTCHA_IMG . $i);
                    }
                }
            }
            else { return false;}
        }
        return true;
    }
    
    static function generate()
    {
        if(isset($_SESSION['captcha'])) { unset($_SESSION['captcha']);}

        if(self::crypt()){
            if(!isset($_SESSION['path'])) { 
                $list = file(CAPTCHA_IMG . 'list');
                foreach ($list as $l){
                    $_SESSION['path'][] = $l;
                }
            }
            return $_SESSION['captcha'] = $_SESSION['path'][mt_rand(0, count($_SESSION['path'])-1)];
        }

    }
    
    static function match($input)
    {

        $start = strrpos($_SESSION['captcha'], '/') + 1;
        $finish = strrpos($_SESSION['captcha'], '.');
        $decode = Crypt::decode(substr($_SESSION['captcha'],$start, $finish - $start), self::$random);

        if(stripos($decode, $input) !== false){
            return true;
        }
        else { return false; }
    }

}
