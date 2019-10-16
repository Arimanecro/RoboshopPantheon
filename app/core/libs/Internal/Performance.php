<?php

class Performance 
{

    private function speed()
    {
        echo "<strong>Speed:</strong> " .
            number_format((microtime(1) - $_SERVER["REQUEST_TIME_FLOAT"]), 6, '.', '')
            . " ms </br>";
        return $this;
    }

    private function memory()
    {
        /*1 Byte = 8 Bit
        1 Kilobyte = 1024 Bytes
        1 Megabyte = 1048576 Bytes
        1 Gigabyte = 1073741824 Bytes*/

        echo "<strong>Memory:</strong> " . round((memory_get_usage()/1048576),6) . " MB </br>";
        return $this;
    }
    
    function all()
    {
        echo "
<link href=\"https://fonts.googleapis.com/css?family=Inconsolata\" rel=\"stylesheet\">
<style>
#bench {display: inline-block;position: absolute; z-index: 0;top: 0;left: 0;font-family: 'Inconsolata', monospace;font-size: 20px;line-height: 28px;background: #ac6cff;}
@keyframes scale-up-center {
            0% {
                -webkit-transform: scale(0.5);
                transform: scale(0.5);
            }
            100% {
                -webkit-transform: scale(1);
                transform: scale(1);
            }
        }
        .scale-up-center {
	        animation: scale-up-center 0.4s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
}
</style>
<div id='bench' class='scale-up-center'>";
        $this->speed()->memory();
        echo "</div>";
    }
}