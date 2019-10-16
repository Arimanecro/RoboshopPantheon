<?php

class Order
{
    static function check()
    {
        $v = new Validators();
        $v->filter(['name' => 'all(min:1, max:15)',
                    'email' => 'email',
                    'address' => 'all(min:1, max:40)',
                    'captcha' => 'captcha',
                    'csrf' => 'csrf'
                    ]);
        if($v->errors()) {
            $_SESSION['inputs'] = $_POST;
            foreach($v->errors() as $error){
                $_SESSION['err_valid'][] = $error;
            }
            Redirect::to();
        }
        else {
            $oid = uniqid().microtime(1);
            ['name' => $name, 'address' => $address, 'email' => $email] = $_POST;
            $items = json_encode($_SESSION['basket']);

            Eloquent::transaction( function() use ($oid, $name, $email, $address, $items)
            {
                Customers::insert(['oid' => $oid, 'name' => $name, 'email' => $email, 'address' => $address, 'time' => time()]);
                Orders::insert(['oid' => $oid, 'items' => $items]);

            });
            $_SESSION['thanks'] = 'Order Completed Successfully!';
            Redirect::to();
        }
    }

    static function showAllOrders()
    {
        $orders = Customers::select('customers.name', 'customers.address', 'customers.email',
                          'customers.time', 'orders.oid', 'orders.items')->join('orders')
                          ->on('oid')->go();

        return View::show('admin', $orders);
    }
}