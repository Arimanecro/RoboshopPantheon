<?php
require_once 'templates/header.php';
?>
<section class="slider_mobile">
        <ul class="rslides">
            <li>
                <h1>Electrolux &apos;Supercyclone&apos; Vacuum Cleaner</h1>
                <p>Comfort is a very important thing nowadays because it is a condition of satisfaction</p>
                <img src="public/img/items/product-1-448.png" alt=""/><a href="/category/Vacuum-Cleaner" class="btn_details">Details</a></li>
            <li>
                <h1>Panasonic HC-VX870 4K Ultra HD Camcorder</h1>
                <p>Comfort is a very important thing nowadays because it is a condition of satisfaction</p>
                <img src="public/img/items/product-34-448.png" alt=""/><a href="/category/cameras" class="btn_details">Details</a></li>
            <li>
                <h1>Siemens Steam Iron</h1>
                <p>Comfort is a very important thing nowadays because it is a condition of satisfaction</p>
                <img src="public/img/items/product-55-448.png" alt=""/><a href="/category/irons" class="btn_details">Details</a></li>
        </ul>
    </section>
<section class="slider">
        <ul class="rslides">
            <li>
                <div class="desc">
                    <h1>Electrolux &apos;Supercyclone&apos; Vacuum Cleaner</h1>
                    <p>Comfort is a very important thing nowadays because it is a condition of satisfaction</p>
                    <a href="/category/Vacuum-Cleaner" class="btn_details">Details</a>
                </div>
                <div class="img_product"><img src="public/img/items/product-1-448.png" alt=""/></div></li>
            <li><div class="desc">
                    <h1>Panasonic HC-VX870 4K Ultra HD Camcorder</h1>
                    <p>Comfort is a very important thing nowadays because it is a condition of satisfaction</p>
                    <a href="/category/cameras" class="btn_details">Details</a>
                </div>
                <div class="img_product"><img src="public/img/items/product-34-448.png" alt=""/></div></li>
            <li><div class="desc">
                    <h1>Siemens Steam Iron </h1>
                    <p>Comfort is a very important thing nowadays because it is a condition of satisfaction</p>
                    <a href="/category/irons" class="btn_details">Details</a>
                </div>
                <div class="img_product"><img src="public/img/items/product-55-448.png" alt=""/></div></li>
        </ul>
    </section>
<section class="latest">
    Latest
</section>
<section class="latest_wrapper ">
<?php
foreach ($data as $row)
{
?>
   <article class="latest__item">
        <span></span>
        <div class="latest__item__img" style="background:url(<?php echo $row->img_medium;?>) no-repeat; background-size:contain; background-position:center;"></div>
       <div class="item__price"><?php echo Currency::showExchangeRate($row->price);?> </div>
       <a href="<?php echo "item".DS.$row->url; ?>">
       <div class="latest__item__desc"><?php echo $row->title;?></div></a>

        <form method="post">
            <input name='id' type='hidden' value='<?php echo $row->id;?>'>
            <input name='title' type='hidden' value='<?php echo $row->title;?>'>
            <input name='price' type='hidden' value='<?php echo $row->price;?>'>
            <input name='img' type='hidden' value='<?php echo $row->img_small;?>'>
            <input name='url' type='hidden' value='<?php echo $row->url;?>'>
            <input name='qty' type='hidden' value='1'>
            <input name="add_basket" type="submit" class="add_basket" value="" <?php echo Basket::bgBasket($row->id)?>>
            <input name="add_wish" type="submit" class="add_wish" value="" <?php echo Basket::bgWishList($row->id)?>>
        </form>
    </article>
<?php } ?>
</section>
<section class="latest" style="margin-top: 38px;">
    Featured
</section>
<section class="latest_wrapper dvesti featured latest_featured">
<?php
shuffle($data);
foreach ($data as $row)
{
    ?>
    <article class="latest__item">
        <div class="latest__item__img" style="background:url(<?php echo $row->img_medium;?>) no-repeat; background-size:contain; background-position:center;"></div>
        <a href="<?php echo "item".DS.$row->url; ?>">
            <div class="item__price"><?php echo Currency::showExchangeRate($row->price);?> </div>
            <div class="latest__item__desc"><?php echo $row->title;?></div></a>
        <form method="post" action=''>
            <input name='id' type='hidden' value='<?php echo $row->id;?>'>
            <input name='title' type='hidden' value='<?php echo $row->title;?>'>
            <input name='price' type='hidden' value='<?php echo $row->price;?>'>
            <input name='img' type='hidden' value='<?php echo $row->img_small;?>'>
            <input name='url' type='hidden' value='<?php echo $row->url;?>'>
            <input name='qty' type='hidden' value='1'>
            <input name="add_basket" type="submit" class="add_basket" value="" <?php echo Basket::bgBasket($row->id)?>>
            <input name="add_wish" type="submit" class="add_wish" value="" <?php echo Basket::bgWishList($row->id)?>>
        </form>
    </article>
<?php } ?>
</section>
<section class="latest specials_mq" style="margin-top: 30px;">
    Special
</section>
<section class="latest_wrapper bestsellers_wraper special_wrapp">
    <?php
    shuffle($data);
    foreach ($data as $row)
    {
        ?>
        <article class="latest__item">
            <span></span>
            <div class="latest__item__img" style="background:url(<?php echo $row->img_medium ;?>) no-repeat; background-size:contain; background-position:center;"></div>
            <div class="latest__item__price"><?php echo $row->price /*Currency::$symbol.' '.number_format($row['price'] * ($_SESSION['currency'] ?: 1), 2)*/;?></div>
            <a href="<?php echo "item".DS.$row->url; ?>">
                <div class="latest__item__desc"><?php echo $row->title;?></div>
                </a>

            <form method="post">
                <input name='id' type='hidden' value='<?php echo $row->id;?>'>
                <input name='title' type='hidden' value='<?php echo $row->title;?>'>
                <input name='price' type='hidden' value='<?php echo $row->price;?>'>
                <input name='img' type='hidden' value='<?php echo $row->img_small;?>'>
                <input name='url' type='hidden' value='<?php echo $row->url;?>'>
                <input name='qty' type='hidden' value='1'>
                <input name="add_basket" type="submit" class="add_basket" value="" <?php echo Basket::bgBasket($row->id)?>>
                <input name="add_wish" type="submit" class="add_wish" value="" <?php echo Basket::bgWishList($row->id)?>>
            </form>
        </article>
    <?php } ?>
</section>
<section class="latest_wrapper bestsellers_wraper latest_featured" style="margin-bottom: 330px;">

</section>
<?php
require_once 'templates/footer.php';
?>