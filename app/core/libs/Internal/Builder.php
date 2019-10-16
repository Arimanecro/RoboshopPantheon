<?php

class Builder
{
    static protected $class;
    static protected $className;
    protected $debugStart = false;
    protected $stack = [];
    protected $count = 0;

    function __construct($class)
    {
        self::$class = $class;
        self::$className = get_class($class);
    }

    function __call($name, $arguments)
    {

        $this->stack();


        $this->stack[++$this->count] = $name;

        foreach($this->stack as $k => $v){
            if($k == ($this->countInStack)){
            }
            else {
                unset($this->stack[$k]);
                return $this;
            }
        }
    }

    public static function __callStatic($name, $arguments) 
    {

        $t = (new ReflectionClass(static::class))->newInstanceWithoutConstructor() ;
        $t->stack();

        if($t->countInStack){
            forward_static_call_array([self::$className, $name], $arguments);
            return new static(new self::$class);
        }
        else {
            return forward_static_call_array([self::$className, $name], $arguments);
        }
    }

    protected function stack()
    {

        if(!$this->debugStart){

            $this->debugStart = true;

            $debug = debug_backtrace();
            $file = $debug[1]['file'];
            $line = $debug[1]['line'];

            $f = new SplFileObject($file, "r");
            $f->seek($line-1);
            $this->countInStack = count(explode('->',$f->current()))-1;
            $f = null;
            return $this->countInStack;
        }
    }

}