<?php
/*development*/
const DEVELOP_MODE = 0;
const ERRORS = true;
const PERFORMANCE = 0;

/*security*/
const HEALED_HTTP_REQUESTS = ['_GET', '_POST'];

/*PDO settings*/
const CONNECTION = 1;
const PDO_ERR_LOG = 0;

const DB_HOST = 'mysql:host=remotemysql.com';
const DB_NAME = 'bcdpCQt2lU';
const DB_USER = '*******';
const DB_PASS = '*******';

/*Locale*/
const LOCALE = 'en';
const TIMEZONE = 'Europe/London';  //http://php.net/manual/en/timezones.europe.php
const CURRENCY = 1;

/*Theme*/
define ("WELCOME_PAGE", 'welcome');
define ("ERROR_PAGE", '404.html');
define("CAPTCHA_IMG", $_SERVER['DOCUMENT_ROOT']. '/public/img/captcha/');