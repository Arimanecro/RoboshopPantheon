<?php

require_once $_SERVER["DOCUMENT_ROOT"] .'/app/core/bootstrap/Pantheon.php';

Path::get('/', function() { View::show('home',Index::goods());})->alias('#home');

Path::get('item/@item', 'Index->item', '@item');

Path::similar([
    ['category/@title[letters(ex:[-])]',
     'category/@title[letters(ex:[-])]/@page[digits]'],
    ['get', 'get']
], 'Index->category', '@title');

/*Basket*/
Path::get('basket', function(){ View::show('basketView');});
Path::post('basket', 'Basket->order');
Path::put('basket', 'Basket->updateBasket');
Path::delete('basket', 'Basket->deleteFromBasket');
Path::post('basket/deleteall', 'Basket->deleteAll');

Path::similar([
    ['/',
     'category/@title[letters(ex:[-])]',
     'category/@title[letters(ex:[-])]/@page[digits]',
     'item/@item'],
    ['post', 'post', 'post', 'post']
    ], 'Basket->add');

/*WishList*/
Path::get('wishlist', function(){ View::show('wishlistView');});
Path::delete('wishlist', 'WishList->delete');

/*Order*/
Path::get('order', function(){ View::show('orderView');});
Path::post('order', 'Order->check');

/*Search*/
Path::get('search', 'Search->start');
Path::post('search', 'Search->basket');

/*Admin*/
Path::get('admin', 'Order->showAllOrders');

/*Currency*/
Path::post('exchange', 'Currency->userCurrency');