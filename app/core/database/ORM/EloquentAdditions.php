<?php

class EloquentAdditions 
{

    static public $listPreparedConditions, $displayPages;
    private $count;

    function __construct($name)
    {
        $this->className = $name;
    }

    function and($cond_one, $sign=null, $cond_two=null) 
    {
        
        self::$listPreparedConditions[] = $cond_two;
        Eloquent::$query .= " AND $cond_one $sign ?";
        return $this;
    }

    function or($cond_one, $sign=null, $cond_two=null)
    {
        self::$listPreparedConditions[] = $cond_two;
        Eloquent::$query .= " OR $cond_one $sign ?";
        return $this;
    }

    function where($cond_one, $sign=null, $cond_two=null)
    {
        if(is_array($cond_two)){
            foreach ($cond_two as $v){
                self::$listPreparedConditions[] = $v;
            }
        }
        else {
            self::$listPreparedConditions[] = $cond_two;
        }
        if(is_array($cond_two)){
            $marks = explode(',',implode(',', $cond_two));
            Eloquent::$query .= " WHERE $cond_one IN (" . self::autoPrepare($marks) . ")";
        }
        elseif($sign && !is_array($cond_two)){
            Eloquent::$query .= " WHERE $cond_one $sign ? ";
            
        }
        return $this;
    }

    function not($not)
    {
        if(count($not) > 0){
            $c = 0;
            foreach($not as $k => $v) {
                $c++;
                self::$listPreparedConditions[] = $v;
                if($c == 1) {
                    Eloquent::$query .= " WHERE NOT {$k} = ? ";
                }
                else {
                    Eloquent::$query .= " AND NOT {$k} = ? ";
                }
            }
        }
        else {
            self::$listPreparedConditions[] = $not[0];
            Eloquent::$query .= " WHERE NOT " . array_search($not[0], $not) . " = ? ";
        }
        return $this;
    }

    function join($table)
    {
        Eloquent::$query .= " JOIN $table ";
        return $this;
    }

    function leftJoin($table)
    {
        Eloquent::$query .= " LEFT JOIN $table ";
        return $this;
    }

    function rightJoin($table)
    {
        Eloquent::$query .= " RIGHT JOIN $table ";
        return $this;
    }

    function on(...$args)
    {
        if(count($args) == 2){
           $query = " ON {$args[0]} = {$args[1]} ";
        }
        else {
            $query = " USING ({$args[0]}) ";
        }
        Eloquent::$query .= $query;
        return $this;
    }

   function like($word, $like){
       self::$listPreparedConditions[] = $like;
       Eloquent::$query .= " WHERE $word LIKE CONCAT('%',?,'%') ";
       return $this;
    }

    function between($word, $one, $two)
    {
        Eloquent::$query .= " WHERE $word BETWEEN $one AND $two";
        return $this;
    }

    function orderBy($column,$order = 'DESC')
    {
        Eloquent::$query .= " ORDER BY $column $order ";
        return $this;
    }

    function groupBy($column, $agregat='')
    {

        $select = empty($agregat) ? "SELECT " : "SELECT {$agregat}, ";
        $replace = str_replace('SELECT', $select,Eloquent::$query) . " GROUP BY $column ";
        Eloquent::$query = $replace;
        return $this;
    }

    function having($agregat)
    {
        Eloquent::$query .= " HAVING $agregat ";
        return $this;
    }

    function limit($limit=1)
    {
        $this->limitSQL($limit);
        return $this;
    }

    static function autoPrepare($marks)
    {

        return implode(',', array_fill(1, count($marks), '?'));
    }

    private function limitSQL($limit)
    {
        if(MyPDO::cxn()->getAttribute(PDO::ATTR_DRIVER_NAME) == 'pgsql'){

            $q = explode(',', $limit);
            if(count($q)>1){
                return Eloquent::$query .= " LIMIT {$q[1]}  OFFSET {$q[0]}";
            }
            else {
                return Eloquent::$query .= " LIMIT {$q[0]}";
            }

        }
        else {
            return Eloquent::$query .= " LIMIT $limit";
        }
    }

    function paginator()
    {
        $this->cache();
        
        $res = MyPDO::cxn()->prepare(Eloquent::$query);
        $res->execute(self::$listPreparedConditions);
        $num_rows = $res->rowCount();
        $class = new $this->className;
        $pages = new Paginator($num_rows, $class->settingsPaginator['points']=3,array($class->settingsPaginator['perpage'],3,6,9,12,25,50,100,250,'All'),
        $class->settingsPaginator['showNextBack'] = 11, $class->stylePaginator());
        $this->limitSQL("$pages->limit_start,$pages->limit_end");

        $res2 = MyPDO::cxn()->prepare(Eloquent::$query);
        $res2->execute(self::$listPreparedConditions);

        if($res2->rowCount() == 0) { Redirect::to(404); }
        self::$listPreparedConditions = [];

        Paginator::$displayPages = $pages->display_pages();
        if ($num_rows !== 0)
    {
        return $res2->fetchAll();
    }
    else {}
    }

    private function cache() 
    {

        if($this->className::$cache){
            $driver = MyPDO::cxn()->getAttribute(PDO::ATTR_DRIVER_NAME);

            if($driver == 'mysql'){
                $updateTime = MyPDO::cxn()->query("
                     SELECT UNIX_TIMESTAMP(UPDATE_TIME) as unixTime
                     FROM information_schema.tables WHERE TABLE_NAME = '{$this->className}'")
                    ->fetch()->unixTime;
                $this->lastModified($updateTime);
                return;
            }
            elseif ($driver == 'pgsql'){
                $time = MyPDO::cxn()->query("SELECT MAX(pg_xact_commit_timestamp(xmin)) FROM {$this->className} LIMIT 1")
                    ->fetch()->max;
                $updateTime = explode('.',$time)[0] ? (new DateTime(explode('.',$time)[0]))->getTimestamp() : "2678399";
                $this->lastModified($updateTime);
                return;
            }
        }
        else { return false;}
    }
    
    private function lastModified($updateTime)
    {
        $LastModified = date("D, d M Y H:i:s \G\M\T", getdate($updateTime)[0]);

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
            $IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
        }

        if (isset($IfModifiedSince) && ($IfModifiedSince == strtotime($LastModified))) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
            header("Cache-Control: max-age=" . $this->className::$cacheTime);
            echo " ";
            exit;
        }
        else {
            header('Last-Modified: '. $LastModified);
        }
    }
    
    function go() 
    {

        $this->cache();
        $res = MyPDO::cxn()->prepare(Eloquent::$query);

        if(isset(self::$listPreparedConditions[0])){
            if(count(explode(',' ,self::$listPreparedConditions[0])) > 1 &&
                count(self::$listPreparedConditions) == 1
            ){
                self::$listPreparedConditions = explode(',' ,self::$listPreparedConditions[0]);
            }
        }
        $res->execute(self::$listPreparedConditions);
        self::$listPreparedConditions = [];

        if ($res->rowCount() != 0)
        {
            return $res->fetchAll();
        }
    }
}