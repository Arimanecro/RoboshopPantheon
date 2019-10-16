<?php
require_once 'templates/header_for_item.php';
?>
<div class="category cat_item_cat">
    <span><a href="/category/<?php echo $data[0]->category; ?>"><?php echo ucfirst($data[0]->category);?></a></span>
    <span><?php echo $data[0]->title;?></span>
</div>
<?php
foreach ($data as $item)
{
?>
    </div>
    <section class="item_block">
        <figure class="item_block__img" style="background: url(<?php echo $item->img_original;?>) no-repeat center;
            background-size: contain;">
        </figure>
        <div class="item_block__params">
            <h1><?php echo $item->title;?></h1>
            <h2><?php echo Currency::showExchangeRate($item->price); ?></h2>
            <p>Availability: <span>In Stock</span></p>
            <p>Quantity <input form="add_tools" type="text" name="qty" value="1"></p>
        </div>
    </section>
    <form method="post" id="add_tools">
        <input name='id' type='hidden' value='<?php echo $item->id;?>'>
        <input name='title' type='hidden' value='<?php echo $item->title;?>'>
        <input name='price' type='hidden' value='<?php echo $item->price;?>'>
        <input name='img' type='hidden' value='<?php echo $item->img_small;?>'>
        <input name='url' type='hidden' value='<?php echo $item->url;?>'>
        <label for="add_basket" id="btn_basket"><input name="add_basket" id="add_basket" type="submit" value=""></label>
        <label for="add_wish" id="btn_wishlist"><input name="add_wish" id="add_wish" type="submit" value=""></label>
    </form>
    <article class="description_item">
        <h1>Description</h1>
        <p>
            <span><?php echo $item->description;?></span>
        </p>
    </article>
    <?php
}
require_once 'templates/footer.php';
?>