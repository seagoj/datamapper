<?php namespace DataMapper;

use DataMapper\Query;
use DataMapper\SqlStatement;
use Devtools\Format;

abstract class DataMapper
{
    protected $connection;
    protected $table;
    protected $sql;
    protected $params = array();

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->sql        = new SqlStatement;
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public function select($columns = '*')
    {
        if (is_array($columns)) {
            $columns = implode(',', $columns);
        }
        $this->sql->verb = 'SELECT {$columns}';
    }

    public function where($column, $operand, $value, $conjunction = 'AND ')
    {
        if (empty($this->sql->where)) {
            $conjunction = '';
        }

        $this->sql->where[] = " {$conjunction}WHERE {$column} {$operand} :{$column}";
        $this->params[$column] = $value;

        return $this;
    }

    public function andWhere($column, $operand, $value)
    {
        return $this->where($column, $operand, $value, 'AND ');
    }

    public function orWhere($column, $operand, $value)
    {
        return $this->where($column, $operand, $value, 'OR ');
    }

    public function orderBy($column, $direction)
    {
        $direction = strtoupper($direction);
        $this->sql->order[] = " {$column} {$direction}";

        return $this;
    }

    public function limit($count, $start = null)
    {
        $this->sql->limit = " LIMIT {$count}";
        if (!is_null($start--)) {
            $this->sql->limit .= " OFFSET {$start}";
        }
        return $this->run();
    }

    public function join($table, $on, $type = 'JOIN')
    {
        $type = strtoupper($type);
        $on = implode(' ', $on);
        $this->sql->join[] = " {$type} {$table} ON {$on}";

        return $this;
    }

    public function leftOuterJoin($table, $on)
    {
        $this->join($table, $on, ' LEFT OUTER JOIN');
    }

    public function run()
    {
        $this->sql->table = $this->table;
        return $this->connection->result(
            new Query($this->sql->build(), $this->params)
        );
    }
}
