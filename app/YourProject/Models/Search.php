<?php

class Search
{
    static function start()
    {
        if(isset($_GET['word'])) {
            $_SESSION['word'] = $_GET['word'];
            $data = Items::select('id', 'title','category','img_small', 'img_medium', 'url', 'price')
                ->like('title', $_SESSION['word'])
                ->go();
        }
        else {unset($_SESSION['word']); $data = 0;}

        return View::show('searchView', $data);
    }

    static function basket()
    {
        Basket::add();
    }
}