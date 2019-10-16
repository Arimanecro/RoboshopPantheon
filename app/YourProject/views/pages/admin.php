<?php
require_once 'templates/header_for_item.php';

$sum = [];
$currency = Currency::$symbol;

echo "<div style='margin-bottom: 50px;'>";
foreach($data as $orders)
{
    $time = date('F j, Y, H:i:s a', $orders->time);
    echo "
    <br><br>
    <h1>Order ID: {$orders->oid} </h1>
    <br/>
    <p><strong>Name: </strong>{$orders->name}</p>
    <p><strong>Address: </strong>{$orders->address}</p>
    <p><strong>Email: </strong>{$orders->email}</p>
    <p><strong>Time: </strong>{$time}</p>
    <br>
    <h2>Items: </h2><br>";
?>
    <?php
    $items = json_decode($orders->items);
    foreach($items as $i){
        echo "<img src='{$i->img}' style='width:5%;'>
        <p><strong>Title: </strong>$i->title</p>
        <p><strong>Price: </strong>$i->price</p>
        <p><strong>Qty: </strong>$i->qty</p>
        <br>
        ";
        $sum[] = (int)$i->price * (int)$i->qty;
    }
    $total = number_format(array_sum($sum), 2);
    echo "<h3>Total: {$total} {$currency}</h3>";
    echo "<p>=================</p>";
    $sum = [];
}
echo "</div>";
require_once 'templates/footer.php';

