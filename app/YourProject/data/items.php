<?php

class Items extends Eloquent
{
    //static public $cache = true;
    public $settingsPaginator = ['perpage' => 16];

    function stylePaginator()
    {
        return [
            'page' => function($url,$page) {return "<li class='active'><a href='$url'>$page</a></li>";},
            'page_inactive' => function($url,$page) {return "<li ><a href='$url'>$page</a></li>"; }
        ];
    }
}