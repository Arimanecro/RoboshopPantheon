<?php
/*Useful short constants*/

define("ROOT", $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR);
define ("HTTP", isset($_SERVER["HTTPS"]) ? 'https://' : 'http://');
define ("HOST", $_SERVER['HTTP_HOST']);
define ("URI", $_SERVER['REQUEST_URI']);
define ("URL", HTTP . HOST . URI);
define ("PATHS", ROOT . '/app/core/http/Routes/paths/');
define ("TEMPLATE_DIR", ROOT . 'app/core/view/Druid/Templates/inc/');

const DS = DIRECTORY_SEPARATOR;
const PS = PATH_SEPARATOR;