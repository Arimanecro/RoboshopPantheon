<?php

class Index
{
    static function goods():array
    {
        return  Items::select('id', 'title','img_small', 'img_medium', 'url', 'price')
                ->orderBy('RAND()')
                ->limit(4)
                ->go();
    }

    static function category(string $category)
    {
        $data =  Items::select('id', 'title','category','img_small', 'img_medium', 'url', 'price')
            ->where('category', '=', $category)
            ->orderBy('id', 'desc')
            ->paginator();

        return View::show('category', $data);
    }

    static function item($title)
    {
        $data =  Items::all()->where('url', '=', $title)->limit()->go();

        return View::show('item', $data);
    }
}