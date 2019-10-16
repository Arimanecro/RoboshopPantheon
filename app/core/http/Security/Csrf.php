<?php
namespace security;

class CSRF
{
    static function generate_token()
    {
        return $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));

    }

    static function validate_token($input_name)
    {
        if(!empty($_SESSION['token'])){
            if(hash_equals($_SESSION['token'], $input_name)) {
                unset($_SESSION['token']);
                return true;
            }
            else {
                return false;
            }
        }
        else { return false;}

    }
}