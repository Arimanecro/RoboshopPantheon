<?php
require_once 'templates/header_for_item.php';
?>
<section class="latest title_category_items basket_items">Wishlist</section>
<?php
if(isset($_COOKIE['wishlist'])){
    $ids = explode(',', $_COOKIE['wishlist']);
    if($ids < 1) { ?>
        <div class="empty_wish_list">Your Wish List is Empty</div>
    <?php } else
    {
}
    ?>
    <div class="application__content">
    </div>
    <section class="wish_list" style="background: url('/public/img/robo_wish.svg') no-repeat;">
        <div class="wish_list__robo"></div>
        <div class="wrapp_wishlist">
            <?php
            $goods = Items::select('id', 'price','img_small', 'url', 'title')
                     ->where('id', '=', $ids)->go();
            if($goods){
                foreach ($goods as $items) {
?>
    <div class="wish_list__listing">
        <ul>
            <li style="background:url(<?php echo $items->img_small; ?>) no-repeat center;background-size: contain"></li>
            <li><a href="<?php echo "item".DS.$items->url; ?>"><?php echo $items->title; ?></a></li>
            <li><?php echo Currency::$symbol . ' ' . number_format((int)$items-> price * ( (isset($_SESSION['currency'])) ? $_SESSION['currency']: 1), 2); ?></li>
            <form method="post" id="del_basket<?php echo $items->id; ?>">
                <input name="id" type="hidden" value="<?php echo $items->id ?>">
                <label for="del_wish<?php echo $items->id; ?>" class="del_wish">
                <input name="delete" type="submit" id="del_wish<?php echo $items->id ?>" value=""></label>
            </form>
        </ul>
    </div>
                    <?php
                }}

            else { echo 'Incorrect id items';}
            ?>
        </div>

    </section>
    <?php
}
require_once 'templates/footer.php';
?>

