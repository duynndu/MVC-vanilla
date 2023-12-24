<?php
use \app\core\Registry;
class DB
{
    private $columns;
    private $from;
    private $distinct = '';
    private $joins = [];
    private $wheres = [];
    private $groups = [];
    private $havings = [];
    private $orders = [];
    private $limit = '';
    private $offset = '';
    private $db;

    public function __construct($tableName)
    {
        $this->from = $tableName;
        $this->db=Registry::getInstance()->config['db'];
        $this->connect();
    }
    private function connect(){
        $conn=mysqli_connect($this->db['hostName'],$this->db['username'],$this->db['password'],$this->db['dbName']);
        if(!$conn){
             die('Không thể kết nối');
        }
        return $conn;
    }
    private function query($sql){
        return mysqli_query($this->connect(),$sql);
    }
    public static function table($tableName)
    {
        return new self($tableName);
    }

    public function select($columns)
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function distinct()
    {
        $this->distinct = 'DISTINCT';
        return $this;
    }

    public function join($table, $on, $type = '')
    {
        $type = $type . ' ';
        $table = $type . "JOIN $table";
        $on = "ON $on";
        $this->joins[] = [$table, $on, $type];
        return $this;
    }

    public function leftJoin($table, $on)
    {
        $this->join($table, $on, 'LEFT');
        return $this;
    }

    public function rightJoin($table, $on)
    {
        $this->join($table, $on, 'RIGHT');
        return $this;
    }

    public function where($where, $separator = 'AND ')
    {
        if (count($this->wheres) == 0) {
            $where = 'WHERE ' . $where;
            $this->wheres[] = [$where, ''];
        } else {
            $this->wheres[] = [$where, $separator];
        }

        return $this;
    }

    public function orWhere($where)
    {
        $this->where($where, 'OR ');
        return $this;
    }

    public function groupBy($columns)
    {
        if (count($this->groups) == 0) {
            $columns = 'GROUP BY ' . $columns;
        }
        $this->groups = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function having($having, $separator = 'AND ')
    {
        if (count($this->havings) == 0) {
            $having = 'HAVING ' . $having;
            $this->havings[] = [$having, ''];
        } else {
            $this->havings[] = [$having, $separator];
        }
        return $this;
    }

    public function orHaving($having)
    {
        $this->having($having, 'OR ');
        return $this;
    }

    public function orderBy($column, $arrange = 'ASC')
    {
        if (count($this->orders) == 0) {
            $column = 'ORDER BY ' . $column;
        }
        $this->orders[] = [$column, $arrange];
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = 'LIMIT ' . $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = 'OFFSET ' . $offset;
        return $this;
    }

    private function sql()
    {

        $column = implode(",", $this->columns);
        $joins = [];
        foreach ($this->joins as $join) {
            $joins[] = $join[0] . ' ' . $join[1];
        }
        $joins = implode(' ', $joins);
        $wheres = [];
        foreach ($this->wheres as $where) {
            $wheres[] = $where[1] . $where[0];
        }
        $wheres = implode(' ', $wheres);
        $groupBy = implode(",", $this->groups);
        $groupBy = $this->groups ? $groupBy : '';
        print_r($groupBy);
        $havings = [];
        foreach ($this->havings as $having) {
            $havings[] = $having[1] . $having[0];
        }
        $havings = $this->havings ? implode(' ', $havings) : '';
        $orders = [];
        foreach ($this->orders as $order) {
            $orders[] = $order[0] . ' ' . $order[1];
        }
        $orders = $this->orders ? implode(',', $orders) : '';
        $limit = $this->limit;
        $offset = $this->offset;
        $sql = "SELECT {$this->distinct} {$column}
        FROM {$this->from}
        {$joins}
        {$wheres}
        {$groupBy}
        {$havings}
        {$orders}
        {$limit}
        {$offset}
        ";
        echo $sql;
        return $sql;
    }
    public function get(){
        $data=$this->query($this->sql())->fetch_assoc();
        print_r($data);
        return $data;
    }
    public function getAll(){
        $data=[];
        $result=$this->query($this->sql());
        while ($row=$result->fetch_assoc()){
            $data[]=$row;
        }
        return $data;
    }
    public function getLastInsertID(){
        $this->query($this->sql());
        return $this->connect()->insert_id;
    }

}