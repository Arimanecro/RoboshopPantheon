<?php


class Currency {

    static $symbol, $abbrevation;

    public function __construct(){
        if(!isset($_COOKIE['currency'])){
            self::$symbol = $this->getCurrency();}
        else{
            $htmls_symbols = ['&dollar;', '&euro;', '&pound;'];
            if(in_array($_COOKIE['currency'], $htmls_symbols)){
                self::$symbol = $_COOKIE['currency'];
            }
            else {
                self::$symbol = '&pound;';
            }
        }
        //$this->forCurrencyPayPal(self::$symbol);
    }

    private function getCurrency()
    {
        $lang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
        setlocale(LC_MONETARY, $lang);
        $currency = localeconv()['int_curr_symbol'];
        $currency_list = ['EUR', 'USD', 'GBP'];
        $currency = in_array($currency, $currency_list) ? $currency : 'EUR';
        self::$abbrevation = $currency;

        switch ($currency){
            case 'EUR':
                self::$symbol = "&euro;";
                break;
            case 'GBP':
                self::$symbol = '&pound;';
                break;
            case 'USD':
                self::$symbol = '&dollar;';
                break;
        }
        return self::$symbol;
    }

    private function forCurrencyPayPal($currency){
        switch ($currency){
            case '&euro;':
                self::$abbrevation = "EUR";
                break;
            case '&pound;':
                self::$abbrevation = 'GBP';
                break;
            case '&dollar;':
                self::$abbrevation = 'USD';
                break;
        }
    }

    public function userCurrency(){

        if(isset($_POST['dollar'])){
            setcookie("currency", '&dollar;', strtotime( '+ 5 year' ), '/', $_SERVER['HTTP_HOST'], 0, 1);
            $_SESSION['new_currency'] = true;
        }
        elseif(isset($_POST['euro'])){
            setcookie("currency", '&euro;', strtotime( '+ 5 year' ), '/', $_SERVER['HTTP_HOST'], 0, 1);
            $_SESSION['new_currency'] = true;
        }
        elseif(isset($_POST['pound'])){
            setcookie("currency", '&pound;', strtotime( '+ 5 year' ), '/', $_SERVER['HTTP_HOST'], 0, 1);
            unset($_SESSION['currency'], $_SESSION['new_currency']);
        }
        else {
            setcookie("currency", '&pound;', strtotime( '+ 5 year' ), '/', $_SERVER['HTTP_HOST'], 0, 1);
            unset($_SESSION['currency'], $_SESSION['new_currency']);
        }
       Redirect::to(Http\Routes\History::back());
    }

    static function showMenuCurrency(){

        $list_symbols =['euro' => '&euro;', 'pound' => '&pound;', 'dollar' => '&dollar;'];
        if(($key = array_search(self::$symbol,$list_symbols)) !== false){
        unset($list_symbols[$key]);
        foreach ($list_symbols as $k => $v) {
            echo "<label class='$k' for='$k'><input id='$k' name='$k' type='submit' value=''></label>";
        }
        }
        return;
    }

    static function showIconCurrency(){

        $icon = [];
        if(self::$symbol == '&pound;') {
            $icon['abbrevation'] = 'GBP';
            $icon['flag'] = 'gb.svg';
        }
        elseif(self::$symbol == '&euro;') {
            $icon['abbrevation'] = 'EUR';
            $icon['flag'] = 'eu.svg';
        }
        elseif(self::$symbol == '&dollar;') {
            $icon['abbrevation'] = 'USD';
            $icon['flag'] = 'us.svg';
        }
        $_SESSION['cur_abb'] = $icon;
        return $icon;

    }

    static function showExchangeRate($price)
    {
        if(isset($_SESSION['currency'])){
            //var_dump($price / ($_SESSION['currency'])); die;
            return self::$symbol.' '.number_format($price / ($_SESSION['currency'] ?: 1), 2);
        }
        return self::$symbol.' '.$price;

    }

    static function googleCurrencyConverter()
{

    $symbols = [ 'EUR' => '&euro;', 'USD' => '&dollar;', 'GBP'=>'&pound;'];
    if(isset($_COOKIE['currency']) && in_array($_COOKIE['currency'], $symbols))
    {
        $to = array_search( $_COOKIE['currency'], $symbols);
        $exchange = file_get_contents("https://free.currconv.com/api/v7/convert?q=USD_EUR,USD_GBP&compact=ultra&apiKey=a498394ec35761626eaa");
        $result = ($to !=='USD') ? json_decode($exchange,true)["USD_${to}"] : 1;
        
        return $_SESSION['currency'] = substr($result, 0, 4);
    }
    return false;
}
}