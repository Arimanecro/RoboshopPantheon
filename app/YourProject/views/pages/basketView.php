<?php
require_once 'templates/header_for_item.php';
?>
<section class="latest title_category_items basket_items">Basket</section>
<?php
if(!isset($_SESSION['basket']) || count($_SESSION['basket']) < 1) { $sum=0;?>
    <div class="empty_wish_list">Your Basket is Empty</div>
<?php }
else
{
?>
<div class="application__content">
</div>
<section class="wish_list">
    <div class="wish_list__robo" style="background: url('/public/img/robo_wish.svg') no-repeat;"></div>
    <div class="wrapp_wishlist">
<?php
{
$sum = 0;
foreach ($_SESSION['basket'] as $k => $q)
{
    ?>
    <div class="wish_list__listing">
        <ul>
            <li style="background:url(<?php echo $q['img']; ?>) no-repeat center;background-size: contain"></li>
            <li><?php echo $q['title']; ?></li>
            <li><?php echo Currency::$symbol . ' ' . number_format((int)$q['price'] * ( (isset($_SESSION['currency'])) ? $_SESSION['currency']: 1), 2); ?></li>
            <input form="del_up" type="text" name="qty[]" class="qty"
                   value="<?php echo $_SESSION['basket'][$k]['qty']; ?>">
            <form method="post" id="del_basket<?php echo $k; ?>">
                <label for="del_wish<?php echo $k; ?>" class="del_wish">
                    <input name="delete" type="submit" id="del_wish<?php echo $k; ?>" value=""></label>
                <input type="hidden" name="id" value="<?php echo $k; ?>">
            </form>
            <input form="del_up" name="<?php echo $k; ?>[id]" type="hidden"
                   value="<?php echo $q['id']; ?>">
        </ul>
    </div>
    <?php
    $sum += number_format((int)$_SESSION['basket'][$k]['price'] * (isset($_SESSION['currency']) ? $_SESSION['currency']: 1), 2) * $_SESSION['basket'][$k]['qty'];
}
}
?><p class="total">Total: <?php
                            $_SESSION['total'] = $sum;
                            echo Currency::$symbol.' '.number_format($sum, 2, ',', ' ');?></p>
                </div>
                </section>
        <div class="wrapp_btns">
            <form method="post" id="del_up"></form>
            <form method="post" id="deleteall" action="basket/deleteall"></form>
            <label for="orders" class="orders">Order<input form="del_up" type="submit" name="orders" id="orders" value=""></label>
            <label for="del" class="del">Delete All<input form="deleteall" type="submit" name="deleteAll" id="del" value=""></label>
            <label for="update" class="update">Update<input form="del_up" type="submit" name="put" id="update" value=""></label>
        </div>
<?php
}
require_once 'templates/footer.php';
?>