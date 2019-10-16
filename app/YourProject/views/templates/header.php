<?php
// ob_start();
(new Currency());
if(isset($_SESSION['new_currency']) || (!isset($_SESSION['currency']))) {
    Currency::googleCurrencyConverter();
    unset($_SESSION['new_currency']);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Robo Store</title>
    <link rel="shortcut icon" href="./public/img/favicon.ico" type="image/x-icon" />
    <base href="/">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0" />
    <link href="https://fonts.googleapis.com/css?family=Suez+One" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Athiti" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=PT+Sans+Caption" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script defer src="public/js/responsiveslides.js"></script>
    <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
<nav class="mobile_nav">
    <ul>
        <li>
            <label for="show_menu" id="check_menu"></label></li>
        <li><a href="/wishlist"><span >Wish List</span>&nbsp;<span class="hide"><?php echo Basket::countWish();?></span></a></li>
        <li><a href="/basket"><span>Shopping cart</span>&nbsp;<span class="hide"><?php echo Basket::countBasket();?></span></a></li>
        <style>
            .nav_up ul label:before {
                background:url(../public/img/arrow.png)  no-repeat, url(public/img/<?php echo Currency::showIconCurrency()['flag'];?>) no-repeat;
                background-position: left, right;
                background-size: auto, contain;
            }
            .mobile_nav ul label:before {
                background:url(public/img/arrow.png)  no-repeat, url(public/img/<?php echo Currency::showIconCurrency()['flag'];?>) no-repeat;
                background-position: left, right;
                background-size: auto, contain;
            }
        </style>
        <label for="currency_mobile"><span><?php echo Currency::showIconCurrency()['abbrevation'];?></span>
        </label>
    </ul>
</nav>
<input name="currency_mobile" type="checkbox" id="currency_mobile">
<div class="list_currency_mobile">
    <form method="post" id="form_currency_mobile" action="exchange">
        <?php
        Currency::showMenuCurrency();
        ?>
    </form>
</div>
<input name="show_menu" type="checkbox" id="show_menu">
<nav class="cool_mob_menu">
    <label for="show_menu"></label>
    <nav class="menu_side_mob">
        <ul>
            <a href="./category/Home-Theater-System"><li>Home Theater System</li></a>
            <a href="./category/cell-mobile-wireless-phones"><li>Cell/mobile/wireless phones</li></a>
            <a href="./category/Computers"><li>Computers</li></a>
            <a href="./category/games-movies-music"><li>Games/movies/music</li></a>
            <a href="./category/cameras"><li>Cameras</li></a>
            <a href="./category/sound-devices"><li>Sound devices</li></a>
            <a href="./category/tvs"><li>TVs</li></a>
            <a href="./category/Video-game-consoles"><li>Video game consoles</li></a>
            <a href="./category/Home-security-systems"><li>Home security systems</li></a>
            <a href="./category/irons"><li>Irons</li></a>
            <a href="./category/Vacuum-Cleaner"><li>Vacuum Cleaners</li></a>
        </ul></nav>
</nav>
<div class="gray_line"></div>
<div class="application">
<div class="application__content-wrapper">
        <header>
            <section class="header__mobilelogo">
                    <a href="/"><figure>
                    <svg width="65" height="75">
                        <image xlink:href="./public/img/logo.svg" src="./public/img/logo.svg" width="55" height="75"/>
                    </svg>
                    <figcaption>
                        <p>Robo</p>
                        <p>online store</p>
                    </figcaption>
                </figure></a>
            </section>
            <section class="header__logo">
                <figure>
                    <svg width="110" height="138">
                        <image xlink:href="./public/img/logo.svg" src="./public/img/logo.svg" width="110" height="138"/>
                    </svg>
                    <figcaption>
                        <p>Robo</p>
                        <p>online store</p>
                    </figcaption>
                </figure>
            </section>
            <section class="header__search">
                <form method="get" action="search">
                    <input name="word" type="search" id="search" placeholder="sony">
                    <label for="search_btn"><input id="search_btn" type="submit" value=""></label>
                </form>
            </section>
            <section class="phone"><span>111-222-333</span></section>
        </header>
        <div class="block_cat">
            <svg width="27" height="21">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="-280 372 50 50" enable-background="new -280 372 50 50" xml:space="preserve">
<path fill="#DE8917" d="M-280,379.5v5h50v-5H-280z M-280,394.5v5h50v-5H-280z M-280,409.5v5h50v-5H-280z"/>
</svg>
</svg>Categories</div>
        <nav class="nav_up" style="">
            <ul>
                <li><a href="">Home</a></li>
                <li><a href="/wishlist">Wish List&nbsp;<?php echo Basket::countWish();?></a></li>
                <li><a href="/basket">Shopping cart&nbsp;<?php echo Basket::countBasket();?></a></li>
                <style>
                    .nav_up ul label:before {
                        background:url(public/img/arrow.png)  no-repeat, url(public/img/<?php echo Currency::showIconCurrency()['flag'];?>) no-repeat;
                        background-position: left, right;
                        background-size: auto, contain;
                    }
                    @media all and (max-width:401px) { .nav_up ul label:before  {background:url(public/img/arrow.png) no-repeat; background-position: center; width:15px}
                    }
                </style>
                <label for="currency"><span><?php echo Currency::showIconCurrency()['abbrevation']; ?></span>
                </label>
            </ul>

        </nav>
        <input name="currency" type="checkbox" id="currency">
        <div class="list_currency">
            <form method="post" id="form_currency" action="exchange">
                <?php
                Currency::showMenuCurrency();
                ?>
            </form>
        </div>
        <div class="application__content">
                <nav class="menu_side" style="margin-bottom: 152px;">
                    <ul>
                        <a href="./category/Home-Theater-System">
                            <li>Home Theater System</li>
                        </a>
                        <a href="./category/cell-mobile-wireless-phones">
                            <li>Cell/mobile/wireless phones</li>
                        </a>
                        <a href="./category/Computers">
                            <li>Computers</li>
                        </a>
                        <a href="./category/games-movies-music">
                            <li>Games/movies/music</li>
                        </a>
                        <a href="./category/cameras">
                            <li>Cameras</li>
                        </a>
                        <a href="./category/sound-devices">
                            <li>Sound devices</li>
                        </a>
                        <a href="./category/tvs">
                            <li>TVs</li>
                        </a>
                        <a href="./category/Video-game-consoles">
                            <li>Video game consoles</li>
                        </a>
                        <a href="./category/Home-security-systems">
                            <li>Home security systems</li>
                        </a>
                        <a href="./category/irons">
                            <li>Irons</li>
                        </a>
                        <a href="./category/Vacuum-Cleaner">
                            <li>Vacuum Cleaners</li>
                        </a>
                    </ul>
                    <div class="specials">Specials
                    </div>
                    <?php
                    $goods = Index::goods();
                    shuffle($goods);
                    foreach ($goods as $row)
                    {
                    echo "<article class=\"item_special\">
                    <span class=\"sale\"></span>
                    <div class=\"item__img\" style=\"background:url($row->img_small) no-repeat; background-size: contain;\"></div>
                    <div class=\"item__desc\"><a href=item/{$row->url}>$row->title</a></div>
                    <div class=\"item__price\">".Currency::showExchangeRate($row->price)."</div>
                </article>";
                    } ?>
</nav><main>