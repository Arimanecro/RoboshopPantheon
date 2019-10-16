<?php


class Filters {

    static $msg =[],
            $ex =[];

    static function all(...$args)
    {

        $args[0] = Security\Http::reverseSanitizer(iconv("utf-8", "windows-1251", $args[0]));

            if(!empty($args[1]))
            {
                self::detectException($args[1]);

                if (self::maxMin($args[1]))
                {
                    if (!self::countMaxmin(self::maxMin($args[1]), $args[0]))
                    {
                        self::detectMaxMin($args, 'all');

                        return false;
                    }
                }
       
                    for($i=0; $i <= strlen($args[0])-1; $i++){
                        if(in_array($args[0]{$i}, self::$ex)){
                            if(self::detectErorrs()){
                                self::$msg['all'][] = 'Forbidden symbols: ' . implode(',', self::$ex);
                            }
                            return false;
                        }
                    }

                return $args[0];

            }
            return;
    }

    static function digits(...$args)
    {

        if(!empty($args[1]))
        {
            self::detectException($args[1]);
            if (self::maxMin($args[1]))
            {

                if (!self::countMaxmin(self::maxMin($args[1]), $args[0]))
                {
                    self::detectMaxMin($args, 'digits');
                    return false;
                }

                if(!self::typeOrNot($args, 'digit', 'digits')) { return false; }

                return true;
            }

           elseif ( (count($args[1]) == 1) && (strpos($args[1][0], 'ex:') !== false) ){

                return self::typeOrNot($args, 'digit', 'digits');

            }
            else
            {
                $characters = [];
                for($i=0; $i < strlen($args[0]); $i++){
                    if(!ctype_digit($args[0]{$i})){
                        $characters['others'] = $args[0][$i];
                    }
                    else { $characters['dig'] = $args[0][$i]; }
                }

                if(isset($characters['others']) && !self::typeOrNot($args, 'digit', 'digits'))
                { return false;}

                    if (count($args[1]))
                    {

                        if (count($args[1]) == 1)
                        {
                            $options = [
                                'options' => [
                                    'max_range' => $args[1][0]
                                ],
                            ];
                        } elseif (count($args) >= 2)
                        {
                            $options = [
                                'options' => [
                                    'min_range' => $args[1][0],
                                    'max_range' => $args[1][1]
                                ],

                            ];
                        }
                        if (filter_var($characters['dig'], FILTER_VALIDATE_INT, $options))
                        {
                            return true;
                        } else
                        {
                            if(self::detectErorrs()){
                                if (isset($options['options']['min_range']) && isset($options['options']['max_range']))
                                {
                                    self::$msg['digits'][] = 'Range between : ' . $options['options']['min_range'] .
                                        ' and ' . $options['options']['max_range'];
                                } elseif (isset($options['options']['min_range']))
                                {
                                    self::$msg['digits'][] = 'Range minimum : ' . $options['options']['min_range'];
                                } elseif (isset($options['options']['max_range']))
                                {
                                    self::$msg['digits'][] = 'Range maximum : ' . $options['options']['max_range'];
                                }
                            }
                            return false;
                        }
                    }

            }
        }
        else{

            for ($i = 0; $i < strlen($args[0]); $i++)
            {
                if (!ctype_digit($args[0]{$i}))
                {
                    if(self::detectErorrs()){
                        self::$msg['digits'][] = '- Only digits';
                    }
                    return false;
                }
            }
        }
        
        return true;
    }
    
    static function letters(...$args)
    {

        $args[0] = Security\Http::reverseSanitizer(iconv("utf-8", "windows-1251", $args[0]));

        if(!empty($args[1]))
        {
            self::detectException($args[1]);

            if (self::maxMin($args[1]))
            {

                if (!self::countMaxmin(self::maxMin($args[1]), $args[0]))
                {
                    self::detectMaxMin($args, 'letters');

                    return false;
                }

                if(!self::typeOrNot($args, 'alpha', 'letters')) { return false; }

                return true;
            }
            elseif ( (count($args[1]) == 1) && (strpos($args[1][0], 'ex:') !== false) ){

                return self::typeOrNot($args, 'alpha', 'letters');

            }
        }
        else{
            for ($i = 0; $i < strlen($args[0]); $i++)
            {
                if (!ctype_alpha($args[0]{$i}))
                {
                    if(self::detectErorrs()){
                        self::$msg['letters'][] = '- Only letters';
                    }
                    return false;
                }
            }
        }

        return true;

    }
    
    static function email($email)
    {
        $email = Security\Http::reverseSanitizer($email);
       if(filter_var($email, FILTER_VALIDATE_EMAIL)){
           return true;
       }
        else {
            if(self::detectErorrs()){
                self::$msg['email'][] = '- Incorrect email';
            }
            return false;
        }
    }

    static function hash($hash, $symbols = false){
        if(!$symbols){
            if(ctype_alnum($hash)) {
                return $hash;
            }
            else {
                if(self::detectErorrs()){
                    self::$msg['hash'][] = "It's not hash.";
                }
                return false;
            }
        }
        else {
            if(ctype_alnum($hash) || ctype_punct($hash)){
                if(self::detectErorrs()){
                    self::$msg['hash'][] = "It's not hash.";
                }
                return false;
            }
            else {
                return $hash;
            }
        }

    }

    static function csrf($token)
    {
        if(security\CSRF::validate_token($token)){
            return true;
        }
        else {
            if(self::detectErorrs()){
                self::$msg['csrf'][] = '- CSRF token no match!';
            }
            return false; 
        }
    }

    static function captcha($input)
    {
        if(Captcha::match($input)){
            return true;
        }
        else {
            if(self::detectErorrs()){
                self::$msg['captcha'][] = '- Incorrect captcha entered!';
            }
            return false;
        }
    }

    private static function maxMin($string)
    {


        $maxmin = [];
        $checker = [];

        foreach ($string as $val)
        {
            if (strpos($val, 'max:') !== false)
            {
                $checker[] = $val;

            } elseif (strpos($val, 'min:') !== false)
            {
                $checker[] = $val;
            }
        }

        if($checker){

            foreach($checker as $v){
                $t = explode(':', trim($v));
                $maxmin[$t[0]] = $t[1];
                unset($t);
            }

            unset($checker);
            return $maxmin;
        }
        else {

            return false;
        }
    }

    private static function countMaxmin($maxmin, $text)
    {

        if(isset($maxmin['min']) && !isset($maxmin['max'])) {
            $options =[
                'options' => [
                    'min_range' => $maxmin['min']
                ],
            ];
        }
        elseif(isset($maxmin['max']) && !isset($maxmin['min'])) {
            $options =[
                'options' => [
                    'max_range' => $maxmin['max']
                ],
            ];
        }
        else {
            $options = [
                'options' => [
                    'min_range' => $maxmin['min'],
                    'max_range' => $maxmin['max']
                ]
            ];
        }

        return filter_var(mb_strlen($text), FILTER_VALIDATE_INT, $options);
    }

    private static function detectException(array $exc)
    {
        $parse = '';

        foreach ($exc as $k => $e){
            if (strpos($e, 'ex:') !== false) {
                for($i=0; $i < strlen($e); $i++){
                    if($e{$i} == '[' ){
                        $start = $i;
                    }
                }
                $parse = trim(substr($e, $start+1 ),']');;
            }
        }
        $exc = array_map(function($t) { return trim($t);},explode('|', $parse));
        return $parse ? self::$ex = $exc : false;
    }

    private static function detectMaxMin($args, $type)
    {

        if(self::detectErorrs()){

            $mm = self::maxMin($args[1]);
            if(array_key_exists('min', $mm) && array_key_exists('max', $mm)){
                self::$msg[$type][] = '- Minimum ' . $mm['min'] . ' characters and maximum '. $mm['max'];
            }
            elseif (array_key_exists('min', $mm)){
                self::$msg[$type][] = '- Minimum ' . $mm['min'] . ' characters ';
            }
            elseif (array_key_exists('max', $mm)){
                self::$msg[$type][] = '- Maximum ' . $mm['max'] . ' characters ';
            }
        }
    }

    private static function typeOrNot($args, $nameMethod, $word)
    {

        $nameMethod = 'ctype_'.$nameMethod;

        for ($i = 0; $i < strlen($args[0]); $i++)
        {
            if (!$nameMethod($args[0]{$i}) && @!in_array($args[0]{$i}, self::$ex) &&
                !empty(self::$ex)
            )
            {
                if(self::detectErorrs()){
                    self::$msg[$word][] = "- only $word and  allowed symbols: " . implode(',', self::$ex);
                }
                self::$ex = [];
                return false;
            } elseif (!$nameMethod($args[0]{$i}) && @!in_array($args[0]{$i}, self::$ex))
            {
                if(self::detectErorrs()){
                    self::$msg[$word][] = "- only $word";
                }
                self::$ex = [];
                return false;
            }
        }
        self::$ex = [];
        return true;
    }
    
    protected static function detectErorrs()
    {
        return array_key_exists('showError', get_class_vars(static::class));
    }

}