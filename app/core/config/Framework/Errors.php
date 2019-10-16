<?php
use \logs\AddInLogFile as AddInLogFile;

ERRORS ? error_reporting(-1) :  error_reporting(0);

if(!ERRORS){
    set_exception_handler (function($exception) {

        if(get_class($exception) == 'PathException'){
            echo $exception->getMessage();
            exit;
        }
        elseif(get_class($exception) == 'TransactionException'){
            AddInLogFile::message($exception->getMessage(), 422);
            MyPDO::cxn()->rollBack();
        }
        elseif(get_class($exception) == 'PDOException') {
            AddInLogFile::message($exception->getMessage(), 422);
        }
    });}