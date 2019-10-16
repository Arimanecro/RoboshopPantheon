<?php

class Basket
{
    static public function add()
    {
        ['id' => $id] = $_POST;
        if(array_key_exists('add_basket', $_POST)){
            $_SESSION['basket'][$id] = $_POST;
        }
        if(array_key_exists('add_wish', $_POST)){
            if(isset($_COOKIE['wishlist'])) {
                $list = explode(",", $_COOKIE['wishlist']);
                if(!in_array($id, $list)) {
                  setcookie("wishlist", $_COOKIE['wishlist'].','.$id, strtotime( '+ 5 year' ), '/', $_SERVER['HTTP_HOST']); }
                }
            else {
                setcookie("wishlist", $id, strtotime( '+ 5 year' ), '/', $_SERVER['HTTP_HOST']);  }
        }
        Redirect::to();
    }

    static function bgBasket($id)
    {
        if(!empty($_SESSION['basket']))
            foreach ($_SESSION['basket'] as $k => $v){
                    if(array_key_exists($id, $_SESSION['basket'])) {
                        return "style='background:url(/public/img/adding_basket_btn.png)'";
                    }
            }
        }

    static function bgWishList($id)
    {
        if(!empty($_COOKIE['wishlist'])){
            if(in_array($id, explode(',', $_COOKIE['wishlist']))) {
                return "style='background:url(./public/img/adding_wish_btn.png)'";
            }
        }
    }

    static  function countBasket(){
        if(isset($_SESSION['basket'])){
            return "[".(count($_SESSION['basket'])) ."]" ?: false ;
        }
    }

    static  function countWish(){
        if(isset($_COOKIE['wishlist'])){
            return "[".(count(explode(',', $_COOKIE['wishlist']))) ."]" ?: false ;
        }
    }

    private static function update()
    {
        $sum = 0;
        $keys_good = array_values($_POST['qty']);
        $ids = array_column($_POST, 'id');
        foreach($ids as $k => $v){
            $_SESSION['basket'][$v]['qty'] = $keys_good[$k];
            $sum  += number_format((int)$_SESSION['basket'][$v]['price'], 2) * (int)$_SESSION['basket'][$v]['qty'];
        }
        $_SESSION['total'] = $sum;
    }

    static function updateBasket(){
        self::update();
        if(isset($_POST['orders'])){
            Redirect::to('order');
        }
        else {
            Redirect::to();
        }

    }

    static function order()
    {
        self::updateBasket();
    }

    static function deleteFromBasket()
    {
        ['id' => $id ] = $_POST;
        unset($_SESSION['basket'][$id]);
        Redirect::to();
    }

    static function deleteAll()
    {
        unset($_SESSION['basket']);
        Redirect::to('/basket');
    }

}