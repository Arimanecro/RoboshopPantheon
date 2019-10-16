<?php

class Druid 
{
    private $pattern;
    private $regEx = [
      'echo' => '(^\[\[(\s*)(?!.*foreach\().*\$(.*))',
      'foreach' => '(^\[\[(\s*)foreach(.*))',
      'iforeach' => '(^(\s*)iforeach(\()(.*))',
      'for' => '(^(\s*)for(\()(.*))',
      'ifor' => '(^(\s*)ifor(\()(.*))', 
      'csrf' => '(^\[\[(\s*)#csrf(\s*))',
      'captcha' =>  '(^(\s*)#captcha(\s*))',
      'paginator' => '(^\[\[(\s*)#paginator(\s*))'
        
    ];
    
    function render($template, $data)
    {

        $template = htmlspecialchars(file_get_contents($template));

        $temp = $this->detectInclude($template);
        $template = $temp;
        $left = self::mb_stripos_all($template, '[[');
        $right = self::mb_stripos_all($template, ']]');

        if($left){
            $combine = $this->coordinatesPhpTags ($right, $left);
        }

if(isset($combine)) {
    foreach ($combine as $value){
        $ex = explode(',' , $value);

        $dd[] = trim(mb_substr($template, $ex[0],  -(mb_strlen($template) - trim($ex[1])), 'UTF-8'));
        }
}
     $dd = (isset($dd)) ? $dd : null;

     $this->replaceByPhp($dd, $template, $data);
    }

    private function coordinatesPhpTags ($right, $left)
{
    $result =[];
    foreach ($right as $k =>$v){
        $l = min(min($left), $v);
        $result[] = "{$l}, {$v}";
        unset($left[array_search($l, $left)]);
    }
    return $result;
}

    private function replaceByPhp($content, $file, $data)
    {

        $temp = $_SERVER['DOCUMENT_ROOT'] . "/". uniqid().microtime(1).'.dd';
        if($content) {
            foreach ($content as $word){
                $match = $this->matchOrNot($word);
                if($match) {
                    $dd_code[] = ($word);
                    $php_code[] = $this->phpRules($match['rule'],($word));
                    $replace = str_replace($dd_code, $php_code,$file);
                }
            }
        }

        if(isset($replace)) {
            $phpCode = ['&lt;?php ', ' ?&gt;'];
            $ddSyntax = ['[[', ']]'];
            $php = str_replace($ddSyntax, $phpCode, $replace);
            file_put_contents($temp, htmlspecialchars_decode($php));

        }
        else {
            file_put_contents($temp, htmlspecialchars_decode($file));
        }

        include $temp;
        unlink($temp);
    }

    private function phpRules($key, $var)
    {
        $rules =
        [
            'echo' => function() use ($var) { return "[[ echo ". ltrim($var, '[['). ";"; },
            'for' => function() use ($var) {
                $elements = explode('__', $var);
                $for = substr(ltrim(trim($elements[0]), ' for('), 0, -1);
                $for = 'for($i=0;' . $for .';$i++) { echo "' . $elements[1] . '"; }';
                return $for;
            },
            'ifor' => function() use ($var) {
                $Ifor = $this->push($var, 'ifor(');
                $for = 'for($i=0;' . $Ifor['loop'][0] .';$i++) { ' . $Ifor['push'] . ' }';
                return $for;
                
            },
            'foreach' => function() use ($var)
            {
                $elements = explode('__', $var);
                $foreachItems = explode(',', rtrim(ltrim(trim(ltrim($elements[0], '[[')), 'foreach('), ')'));

                $foreachItems = array_map(function($arg) { return trim($arg);}, $foreachItems);
                count($foreachItems) == 3
                    ?
                    $conditions = "[[ foreach({$foreachItems[0]} as {$foreachItems[1]} => {$foreachItems[2]})"
                    :
                    $conditions = "[[ foreach({$foreachItems[0]} as {$foreachItems[1]})";
                $conditions .= "{ echo \"". trim($elements[1]) ."\"; } ";

                return $conditions;
            },
            'iforeach' => function() use ($var)
            {
                $Iforeach = $this->push($var, 'iforeach(');

                $IforeachItems = array_map(function($arg) { return trim($arg);}, $Iforeach['loop']);
                
                count($IforeachItems) == 3
                    ?
                    $conditions = "foreach({$IforeachItems[0]} as {$IforeachItems[1]} => {$IforeachItems[2]})"
                    :
                    $conditions = "foreach({$IforeachItems[0]} as {$IforeachItems[1]})";
                $conditions .= "{ " .$Iforeach['push']. " }";
                
                return $conditions;
            },
            'csrf' => function() {
            \security\CSRF::generate_token();
            return "[[ echo \"<input type='hidden' name='csrf' value='{$_SESSION['token']}'>\";"; },
            'captcha' => function() {
                $g = \Captcha::generate();
                return "echo \" <div style='width: 250px;height: 300px;border: #9e9e9e thin solid;background: url($g) center no-repeat;
        background-size:contain; margin: 20px auto;
                                '></div>
    <input type='text' name=\"captcha\" style='display: block;width: 250px;height: 30px;border: #9e9e9e thin solid;
    margin: 20px auto; font-family: \"Spirax\"; font-size: 1.6vw; text-align: center;'
    placeholder='God or human?' autocomplete=\"off\">";
;
            },
            'paginator' => function() { return "[[ echo Paginator::\$displayPages;"; }
        ];
        return $rules[$key]();
    }

    private function push($var, $if)
    {

        $elements = explode('__', $var);
        $foreachItems = explode(PHP_EOL , $elements[1]);

        foreach($foreachItems as $k => $v) {
            if($v !== '') {
                $clear[] = trim(trim($v) . ';', PHP_EOL);
            }
        }

        $push ='';
        $bracket = 0;
        $lastElement = count($clear)-1;

        foreach($clear as $k => $v){
            if(($v != '' && strpos($v, 'if') !== false) ||
                ($v != '' && strpos($v, 'else') !== false)){
                if(!$bracket) {
                    ++$bracket;
                    $push .= trim($v, ';') . '{';
                }
                else {
                    $push .= '}' .trim($v, ';') . '{';
                }

            }
            elseif($v != '') {
                if($k == $lastElement){
                    $push .= $v . '}';
                }
                else {
                    $push .= $v;
                }
            }
        }
        $IforeachItems = explode(',' ,substr(ltrim(trim($elements[0]), $if), 0, -1));
        return ['loop' => $IforeachItems, 'push' => $push];
    }

    private function detectInclude($template)
    {

        $count = 1;
        if ($count)
        {
            if (preg_match('/\[\[(\s*)\+(\s*)\w+(\s*)\]\]/ui', $template, $matches))
            {
                $explode = trim(rtrim(explode('+', $matches[0])[1], ']]')) . '.dd';
                $this->pattern = str_replace($matches[0], htmlspecialchars(file_get_contents(TEMPLATE_DIR . $explode)), $template);
                $this->detectInclude($this->pattern);

            } else
            {
                $count = 0;

            }
        }
        return  $this->pattern ?: $template;
    }

    function matchOrNot($code)
    {
        foreach ($this->regEx as $rule => $reg){
            if(preg_match($reg, $code, $match)){
                return ['rule' => $rule, 'match' => $match];
            }
        }
    }
    
    static function isDruid($file)
    {
        
        return substr($file, -3, 3) == '.dd' ?: false;
    }

    static function mb_stripos_all($haystack, $needle) 
    {

        $s = 0;
        $i = 0;

        while(is_integer($i)) {

            $i = mb_stripos($haystack, $needle, $s);

            if(is_integer($i)) {
                $aStrPos[] = $i;
                $s = $i + mb_strlen($needle);
            }
        }
        if(isset($aStrPos)) {
            return $aStrPos;
        } else {
            return false;
        }
    }
}