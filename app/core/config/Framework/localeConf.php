<?php

if(defined('TIMEZONE')){
    date_default_timezone_set(TIMEZONE);
}

LOCALE ? setlocale(LC_ALL, LOCALE) : setlocale(LC_ALL, substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5)); //Framework detect language automatically