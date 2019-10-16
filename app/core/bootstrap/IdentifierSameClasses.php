<?php

namespace bootstrap;

class IdentifierSameClasses 
{
    
    function __construct()
    {
        $this->performance();

        ini_set('include_path',file_get_contents(PATHS . 'dirs.txt'));

        spl_autoload_register(function ($classname)
        {
            if(DIRECTORY_SEPARATOR == '/'){
                $classname = "/" . str_replace("\\", "/", $classname); 
             }
             else {
                 $classname = DIRECTORY_SEPARATOR . $classname;
             } 
            
            $clones = [];

            $handle = fopen(PATHS .'file.txt', "r");

                while (($buffer = fgets($handle)) !== false) {
                    if(count($clones) <= 1) {
                        if(stripos($buffer, $classname.".php") !== false) {
                            $clones[] = $buffer;
                        }
                    }

                }
                fclose($handle);

            if(count($clones) > 1) {

                echo "
<style>
.error_clone {
display: block;background:rgb(247, 91, 91);position: 
absolute;top:0;right:0;z-index:33;
font-family: 'Inconsolata', monospace;
font-size: 20px;line-height: 28px;
}
@keyframes scale-up-center {0% {
-webkit - transform: scale(0.5);
transform: scale(0.5);}100 % {-webkit - transform: scale(1);transform: scale(1);}}
.scale-up-center {
	        animation: scale-up-center 0.4s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
}
</style >
<div class='error_clone scale-up-center'>
<p>Error: Your project contains " .count($clones). " 
                identical class names -- <strong>{$classname}</strong>: <br>";
                for($i=0; $i < count($clones); $i++)
                {
                    echo $clones[$i]."<br>";
                }
                echo "</p></div><br>";
                echo "<script>
let error = document.querySelector('.error_clone');
error.remove();
document.body.appendChild(error); 
</script>";
                exit;
            }
            else {
                if(!empty($clones[0])) {
                    require_once trim($clones[0], PHP_EOL);
                }
            }
        });
    }

    private function performance()
    {
        if (DEVELOP_MODE){ $this->collection(); }

    }

    static function genFileData($file) {

        $handle = fopen($file, "r");

        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                yield $buffer;
            }
        }

        fclose($handle);
    }

    private function collection()
    {
        $trunk = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'app';
        $dir="";
        $file="";
        foreach (new \RecursiveIteratorIterator(
                     new \RecursiveDirectoryIterator($trunk, \FilesystemIterator::SKIP_DOTS),
                     \RecursiveIteratorIterator::SELF_FIRST) as $f)
        {
            $f->isDir()  ? $dir .= $f->__toString() . PATH_SEPARATOR
                :
                $file .= $f->getPathname(). PHP_EOL ;
        }

        file_put_contents(PATHS . 'dirs.txt', $dir);
        file_put_contents(PATHS . 'file.txt', $file);

    }
}