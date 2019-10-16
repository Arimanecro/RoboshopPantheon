<?php
require_once 'templates/header.php';
?>
<section class="latest title_category_items">
    <?php
    echo ucwords(str_replace("-", " ", $data[0]->category));?>
</section>
<?php
$data = array_chunk($data, 4);
foreach($data as $v) {echo "<section class=\"latest_wrapper category_items\">";
foreach ($v as $vv) {
echo "<article class=\"latest__item \" style='border-top:0px; border-bottom: rgba(0,0,0, 0.27) thin solid;'>
<div class=\"latest__item__img\" style=\"background:url($vv->img_medium) no-repeat; background-size:contain; background-position:center;\"></div>
<div class=\"latest__item__price\">".Currency::showExchangeRate($vv->price)."</div>
<div class=\"latest__item__desc\"><a href='item/$vv->url'> $vv->title</a></div>

<form method=\"post\">
    <input name='id' type='hidden' value='$vv->id'>
            <input name='title' type='hidden' value='$vv->title'>
            <input name='price' type='hidden' value='$vv->price'>
            <input name='img' type='hidden' value='$vv->img_small'>
            <input name='url' type='hidden' value='$vv->url'>
            <input name='qty' type='hidden' value='1'>
    <input name=\"add_basket\" type=\"submit\" class=\"add_basket\" value=\"\"" .Basket::bgBasket($vv->id).">
    <input name=\"add_wish\" type=\"submit\" class=\"add_wish\" value=\"\"" .Basket::bgWishList($vv->id). ">
</form>
</article>";
}
echo "</section>";
}
echo "<div class=\"paginator\"><ul>" . Paginator::$displayPages. "</ul></div>";
?>
<section class="latest_wrapper bestsellers_wraper latest_featured" style="display:block;margin-bottom: 330px;"></section>
<?php
require_once 'templates/footer.php';
?>
