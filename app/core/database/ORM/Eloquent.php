<?php
include 'Paginator.php';

class Eloquent
{
    static protected $table;
    static protected $primary = 'id';
    static public $query, $distincts;
    static protected $conditions;
    static public $cache = null;
    static public $cacheTime = 3600;
    static public $rewrite = 0;
    public $settingsPaginator = ['perpage' => 2, 'points' => 5, 'showNextBack' => 2]; // may be protected?
    
    static protected function Tab()
    {
        return static::$table ?: array_reverse(explode('\\', static::class))[0];
    }

    static function all()
    {
        self::$query = "SELECT * FROM " . self::Tab();
        return new EloquentAdditions(static::class);
    }

    static function select(...$what) {

        $columns = $what ?: static::$primary;

        if(is_array($columns)) {
            $columns = implode(',', $columns);
        }
        self::$query = "SELECT " . $columns . " FROM " . self::Tab();
        return new EloquentAdditions(static::class);
    }

    static function distinct(...$special){
            self::$distincts = implode(',', $special);
            self::$query = "SELECT DISTINCT " . self::$distincts . " FROM " . self::Tab() ;
            return new EloquentAdditions(static::class);
     
    }

    static function insert($what)
    {
        $columns = [];
        $values = [];
        $marksQ = [];
        $query = '';

        if(isset($what[0]) && is_array($what[0])){
            foreach($what as $v){
                foreach($v as $key => $val){
                    if(!in_array($key, $columns)){
                        $columns[] = $key;
                    }
                    $values[] = $val;
                }
            }
        $keys = [];
        $autoprepare =[];

            foreach($what as $k =>$v){
                    if(is_array($v)){
                        foreach($v as $key2 => $value2){
                            $keys[] = $key2;
                        }
                    }
                }

        $keys = array_unique($keys);
        $merge = [];
        foreach ($values as $k => $v){
            if(is_array($v)){
                $merge = array_merge($merge, array_values($v));
            }
        }

        if(count($what) > 1){

        foreach($what as $q){
                        $marksQ[] = "(" . EloquentAdditions::autoPrepare($keys) .")";
                    }
                    $query = "INSERT INTO " .self::Tab(). "(" . implode(',', $keys) .") 
        VALUES " . implode(',', $marksQ);
                }
                else {

                    $query = "INSERT INTO " .self::Tab(). "(" . implode(',', $keys) .") 
        VALUES(". EloquentAdditions::autoPrepare($what[0]) .")" ;
                }
        }
        else {
            foreach ($what as $k => $v){
                $values[] = $v;
            }
            $query = "INSERT INTO " .self::Tab(). "(" . implode(',', array_keys($what)) .") 
        VALUES (" . EloquentAdditions::autoPrepare($what).")";
        }
            $res = MyPDO::cxn()->prepare($query);
            $res->execute($values);

    }

    static function update(...$what){

        $columns = [];
        
        foreach($what as $v){
            foreach($v as $key => $val){
                $columns[] = $key . '=?';
                EloquentAdditions::$listPreparedConditions[] = $val;
            }
        }

        self::$query = "UPDATE " .self::Tab(). " SET " . implode(',', $columns);

        return new EloquentAdditions(static::class);

    }

    static function delete(){
        self::$query = "DELETE FROM " . self::Tab() ;
        return new EloquentAdditions(static::class);
    }

    static function transaction($queries)
    {

            MyPDO::cxn()->beginTransaction();
            try {
                $queries();
                MyPDO::cxn()->commit();
            }
            catch (PDOException $e){
                MyPDO::cxn()->rollBack();
                throw new TransactionException($e->getMessage());
    }
    }
    
    function stylePaginator()
    {
        self::$rewrite = 1;
        $paginator_page = "display: flex;width: 45px;justify-content: center;
            align-items: center;font-family: Open Sans Condensed;font-size: 20px;font-weight: 700;color: white;background: #000;";

        $inactive = "background: #00000082;";

        return [
            'next' => function($url) use ($paginator_page) { return "
<a href='$url' style='$paginator_page'> >> </a></div></div>"; },
            'next_inactive' => function() use ($paginator_page, $inactive) { return "
<a href='' style='$paginator_page'> >> </a></div></div> "; },
            'back' => function($url) use ($paginator_page) { return "
<div style='text-align: center;'><div style='display: inline-flex;
            justify-content: center;
            height: 42px;'>
<a href='$url' style='$paginator_page'> << </a>"; },
            'back_inactive' => function() use ($paginator_page, $inactive) { return "
<div style='text-align: center;'><div style='display: inline-flex;
            justify-content: center;
            height: 42px;'>
<a href='' style='$paginator_page'> << </a>"; },
            'page' => function($url,$page) use ($paginator_page, $inactive) {return "<a href='$url' style='$paginator_page $inactive'>$page</a>"; },
            'page_inactive' => function($url,$page) use ($paginator_page) {return "<a href='$url' style='$paginator_page'>$page</a>"; }
        ];
    }
}