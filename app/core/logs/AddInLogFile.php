<?php
namespace logs;

class AddInLogFile
{
    static function message($exception, $codeError=null)
    {
        if(PDO_ERR_LOG){
            $string = $exception." | ".date('Y-m-D H:i:s', time()).PHP_EOL;
            $logFile = $_SERVER['DOCUMENT_ROOT'] . "/app/core/logs/pdoLogs.log";
            if(filesize($logFile) > 500000){
                file_put_contents($logFile, '');
            }
            error_log($string, 3, $logFile);
        }

        \View::show('Error.html', null, $codeError ?: '');
        exit;
    }

}