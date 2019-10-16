<?php

set_include_path(dirname($_SERVER['DOCUMENT_ROOT']).'/app');

register_shutdown_function(function(){
    if(PERFORMANCE) {
        (new Performance())->all();
    }
    return Path::unexistPath();
});
/*ini_settings*/
if(isset($_SERVER["HTTPS"])) { ini_set('session.cookie_secure',0); }
ini_set('session.cookie_httponly',1);
ini_set('session.use_only_cookies',1);

session_cache_limiter('private_no_expire');
if(!isset($_SESSION)) { session_start(); }