<?php

use app\core\AppException;
use app\core\Registry;

class DB
{
    private $columns = [];
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
    private $connect;
    private $type;

    public function __construct($tableName)
    {
        $this->from = $tableName;
        $this->db = Registry::getInstance()->config['db'];
        $this->connect = $this->connect();
        $this->type = 'GET';
    }

    private function connect()
    {
        $conn = mysqli_connect($this->db['hostName'], $this->db['username'], $this->db['password'], $this->db['dbName']);
        if (!$conn) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }
        return $conn;
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
        $table = $type . "JOIN $table";
        $on = "ON $on";
        $this->joins[] = $table . ' ' . $on;
        return $this;
    }

    public function leftJoin($table, $on)
    {
        $this->join($table, $on, 'LEFT ');
        return $this;
    }

    public function rightJoin($table, $on)
    {
        $this->join($table, $on, 'RIGHT ');
        return $this;
    }

    public function where($where, $separator = 'AND ')
    {
        if($where){
            $where = preg_replace("/([^,=\\s]+)=('?)([^',\\s]*)('?)/", "$1='$3'", $where);
            if (count($this->wheres) == 0) {
                $where = 'WHERE ' . $where;
                $this->wheres[] = $where;
            } else {
                $this->wheres[] = $separator . $where;
            }
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
            $this->havings[] = $having;
        } else {
            $this->havings[] = $separator . $having;
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
        $this->orders[] = $column . ' ' . $arrange;
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

    private function getSQL($type = 'GET', $data = []): string
    {
        $column = implode(",", $this->columns);
        $joins = implode(' ', $this->joins);
        $wheres = implode(' ', $this->wheres);
        $groupBy = implode(",", $this->groups);
        $havings = implode(' ', $this->havings);
        $orders = implode(',', $this->orders);
        $sql = '';
        if ($type == 'GET') {
            $sql = "SELECT {$this->distinct} {$column}
            FROM {$this->from}
            {$joins}
            {$wheres}
            {$groupBy}
            {$havings}
            {$orders}
            {$this->limit}
            {$this->offset}
            ";
        }
        if ($type == 'ADD' || $type == 'INSERT') {
            $col = implode(",", array_map(fn($value) => "`$value`", array_keys($data)));
            $value = implode(",", array_map(fn($value) => "'$value'", array_values($data)));
            $sql = "INSERT INTO {$this->from} ({$col})
            VALUE ({$value})";
        }
        if ($type == 'EDIT' || $type == 'UPDATE') {
            $data = implode(",", array_map(fn($key, $value) => $key . '=' . $value, array_keys($data), array_values($data)));
            $sql = "UPDATE {$this->from}
            SET {$data}
            {$joins}
            {$wheres}
            {$groupBy}
            {$havings}";
        }
        if ($type == 'DELETE' || $type == 'REMOVE') {
            $sql = "DELETE FROM {$this->from}
            {$joins}
            {$wheres}
            {$groupBy}
            {$havings}";
        }
        echo $sql;
        return $sql;
    }

    public function get()
    {
        $data = $this->query($this->getSQL())->fetch_assoc();
        return $data;
    }

    public function getAll()
    {
        $data = [];
        $result = $this->query($this->getSQL());
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getLastInsertID()
    {
        return $this->connect->insert_id;
    }

    public function delete($where=null)
    {
        $this->where($where);
        return $this->query($this->getSQL('DELETE'));
    }

    public function update($data = [],$where=null)
    {
        $this->where($where);
        return $this->query($this->getSQL('UPDATE', $data));
    }

    /**
     * @throws AppException
     */
    public function insert($data = [])
    {
        return $this->query($this->getSQL('INSERT', $data));
    }

    /**
     * @throws AppException
     */
    private function query($sql)
    {
        $query = mysqli_query($this->connect, $sql);
        if ($query) {
            return $query;
        } else {
            throw new AppException(mysqli_error($this->connect));
        }
    }
    public function __destruct()
    {
        $this->connect->close();
    }

}