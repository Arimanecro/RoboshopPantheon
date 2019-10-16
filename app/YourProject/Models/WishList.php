<?php

class WishList
{
    static function delete()
    {
        ['id' => $id] = $_POST;
        $list = explode(",", $_COOKIE['wishlist']);
        $key = array_search($id, $list);
        unset($list[$key]);
        setcookie("wishlist", implode(',', $list), strtotime('+ 5 year'), '/', $_SERVER['HTTP_HOST']);
        Redirect::to();
    }
}