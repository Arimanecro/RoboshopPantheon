<?php
require_once 'templates/header_for_item.php';
if(count($_SESSION['basket']) > 0) {

    \security\CSRF::generate_token();

    if(isset($_SESSION['thanks'])) {
        echo  "<div class='errors no_errors'><p>$_SESSION[thanks]</p></div>";
        unset($_SESSION['thanks'], $_SESSION['inputs'], $_SESSION['basket']);
    }

	if(!empty($_SESSION['err_valid'])) {
        echo "<div class='errors'>
			<p>ERRORS:</p>";
        foreach($_SESSION['err_valid'] as $v) {
            echo "<p>$v</p>";
        }
        echo "</div>";
        unset($_SESSION['err_valid']);}
?>
    <div class="wrapp_order">
    <div class="robo_order"></div>
    <form method="post" id="order">
        <input name="name" type="text" placeholder="__FULL NAME__" value="<?php if(isset($_SESSION['inputs']['name'])) echo $_SESSION['inputs']['name'];?>">
        <input name="address" type="text" placeholder="__ADDRESS__" value="<?php if(isset($_SESSION['inputs']['address'])) echo $_SESSION['inputs']['address'];?>">
        <input name="email" type="text" placeholder="__EMAIL__" value="<?php if(isset($_SESSION['inputs']['email'])) echo $_SESSION['inputs']['email'];?>">
        <p>CAPTCHA</p>
        <div class="captcha" style="background: url(<?php echo Captcha::generate();?>) no-repeat center center;background-size:contain;"></div>
        <input name="captcha" type="text" placeholder="ROBOT OR HUMAN ?">
        <input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
        <div class="pp"></div>
        <label for="check_order" class="check_order">ORDER<input id="check_order" name="check_order" type="submit"></label>
    </form>
</div>
<?php
unset($_SESSION['inputs']);
}
else { Redirect::to("/"); }
require_once 'templates/footer.php';
?>