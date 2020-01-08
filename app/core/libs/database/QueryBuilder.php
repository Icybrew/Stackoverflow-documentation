<?php


namespace App\Core\Libs\Database;

use PDO;

class QueryBuilder
{
    public $pdo = null;

    protected $select = '*';
    protected $from = null;
    protected $where = null;
    protected $limit = null;
    protected $offset = null;
    protected $join = null;
    protected $orderBy = null;
    protected $groupBy = null;
    protected $having = null;
    protected $grouped = false;
    protected $numRows = 0;
    protected $insertId = null;
    protected $query = null;
    protected $error = null;
    protected $result = [];
    protected $prefix = null;

    protected $op = ['=', '!=', '<', '>', '<=', '>=', '<>', 'LIKE'];

    protected $cache = null;

    protected $cacheDir = null;

    protected $queryCount = 0;

    protected $debug = true;

    protected $transactionCount = 0;



    public function __construct($connection) {
        $this->pdo = $connection;
    }

    public function from($table)
    {
        if (is_array($table)) {
            $from = '';
            foreach ($table as $key) {
                $from .= $this->prefix . $key . ', ';
            }
            $this->from = rtrim($from, ', ');
        } else {
            if (strpos($table, ',') > 0) {
                $tables = explode(',', $table);
                foreach ($tables as $key => &$value) {
                    $value = $this->prefix . ltrim($value);
                }
                $this->from = implode(', ', $tables);
            } else {
                $this->from = $this->prefix . $table;
            }
        }
        return $this;
    }

    public function select($fields)
    {
        $select = (is_array($fields) ? implode(', ', $fields) : $fields);
        $this->select = ($this->select == '*' ? $select : $this->select . ', ' . $select);
        return $this;
    }

    public function max($field, $name = null)
    {
        $func = 'MAX(' . $field . ')' . (! is_null($name) ? ' AS ' . $name : '');
        $this->select = ($this->select == '*' ? $func : $this->select . ', ' . $func);
        return $this;
    }

    public function min($field, $name = null)
    {
        $func = 'MIN(' . $field . ')' . (! is_null($name) ? ' AS ' . $name : '');
        $this->select = ($this->select == '*' ? $func : $this->select . ', ' . $func);
        return $this;
    }

    public function sum($field, $name = null)
    {
        $func = 'SUM(' . $field . ')' . (! is_null($name) ? ' AS ' . $name : '');
        $this->select = ($this->select == '*' ? $func : $this->select . ', ' . $func);
        return $this;
    }

    public function count($field, $name = null)
    {
        $func = 'COUNT(' . $field . ')' . (! is_null($name) ? ' AS ' . $name : '');
        $this->select = ($this->select == '*' ? $func : $this->select . ', ' . $func);
        return $this;
    }

    public function avg($field, $name = null)
    {
        $func = 'AVG(' . $field . ')' . (! is_null($name) ? ' AS ' . $name : '');
        $this->select = ($this->select == '*' ? $func : $this->select . ', ' . $func);
        return $this;
    }

    public function join($table, $field1 = null, $op = null, $field2 = null, $type = '')
    {
        $on = $field1;
        $table = $this->prefix . $table;
        if (! is_null($op)) {
            $on = (! in_array($op, $this->op) ? $field1 . ' = ' . $op : $field1 . ' ' . $op . ' ' . $field2);
        }
        $this->join = (is_null($this->join))
            ? ' ' . $type . 'JOIN' . ' ' . $table . ' ON ' . $on
            : $this->join . ' ' . $type . 'JOIN' . ' ' . $table . ' ON ' . $on;
        return $this;
    }

    public function innerJoin($table, $field1, $op = '', $field2 = '')
    {
        $this->join($table, $field1, $op, $field2, 'INNER ');
        return $this;
    }

    public function leftJoin($table, $field1, $op = '', $field2 = '')
    {
        $this->join($table, $field1, $op, $field2, 'LEFT ');
        return $this;
    }

    public function rightJoin($table, $field1, $op = '', $field2 = '')
    {
        $this->join($table, $field1, $op, $field2, 'RIGHT ');
        return $this;
    }

    public function fullOuterJoin($table, $field1, $op = '', $field2 = '')
    {
        $this->join($table, $field1, $op, $field2, 'FULL OUTER ');
        return $this;
    }

    public function leftOuterJoin($table, $field1, $op = '', $field2 = '')
    {
        $this->join($table, $field1, $op, $field2, 'LEFT OUTER ');
        return $this;
    }

    public function rightOuterJoin($table, $field1, $op = '', $field2 = '')
    {
        $this->join($table, $field1, $op, $field2, 'RIGHT OUTER ');
        return $this;
    }

    public function where($where, $op = null, $val = null, $type = '', $andOr = 'AND')
    {
        if (is_array($where) && ! empty($where)) {
            $_where = [];
            foreach ($where as $column => $data) {
                $_where[] = $type . $column . '=' . $this->escape($data);
            }
            $where = implode(' ' . $andOr . ' ', $_where);
        } else {
            if (is_null($where) || empty($where)) {
                return $this;
            }
            if (is_array($op)) {
                $params = explode('?', $where);
                $_where = '';
                foreach ($params as $key => $value) {
                    if (! empty($value)) {
                        $_where .= $type . $value . (isset($op[$key]) ? $this->escape($op[$key]) : '');
                    }
                }
                $where = $_where;
            } elseif (! in_array($op, $this->op) || $op == false) {
                $where = $type . $where . ' = ' . $this->escape($op);
            } else {
                $where = $type . $where . ' ' . $op . ' ' . $this->escape($val);
            }
        }
        if ($this->grouped) {
            $where = '(' . $where;
            $this->grouped = false;
        }
        $this->where = (is_null($this->where))
            ? $where
            : $this->where . ' ' . $andOr . ' ' . $where;
        return $this;
    }

    public function orWhere($where, $op = null, $val = null)
    {
        $this->where($where, $op, $val, '', 'OR');
        return $this;
    }

    public function notWhere($where, $op = null, $val = null)
    {
        $this->where($where, $op, $val, 'NOT ', 'AND');
        return $this;
    }

    public function orNotWhere($where, $op = null, $val = null)
    {
        $this->where($where, $op, $val, 'NOT ', 'OR');
        return $this;
    }

    public function whereNull($where)
    {
        $where = $where . ' IS NULL';
        $this->where = (is_null($this->where))
            ? $where
            : $this->where . ' ' . 'AND ' . $where;
        return $this;
    }

    public function whereNotNull($where)
    {
        $where = $where . ' IS NOT NULL';
        $this->where = (is_null($this->where))
            ? $where
            : $this->where . ' ' . 'AND ' . $where;
        return $this;
    }

    public function grouped(Closure $obj)
    {
        $this->grouped = true;
        call_user_func_array($obj, [$this]);
        $this->where .= ')';
        return $this;
    }

    public function in($field, array $keys, $type = '', $andOr = 'AND')
    {
        if (is_array($keys)) {
            $_keys = [];
            foreach ($keys as $k => $v) {
                $_keys[] = (is_numeric($v) ? $v : $this->escape($v));
            }
            $where = $field . ' ' . $type . 'IN (' . implode(', ', $_keys) . ')';
            if ($this->grouped) {
                $where = '(' . $where;
                $this->grouped = false;
            }
            $this->where = (is_null($this->where))
                ? $where
                : $this->where . ' ' . $andOr . ' ' . $where;
        }
        return $this;
    }

    public function notIn($field, array $keys)
    {
        $this->in($field, $keys, 'NOT ', 'AND');
        return $this;
    }

    public function orIn($field, array $keys)
    {
        $this->in($field, $keys, '', 'OR');
        return $this;
    }

    public function orNotIn($field, array $keys)
    {
        $this->in($field, $keys, 'NOT ', 'OR');
        return $this;
    }

    public function between($field, $value1, $value2, $type = '', $andOr = 'AND')
    {
        $where = '(' . $field . ' ' . $type . 'BETWEEN ' . ($this->escape($value1) . ' AND ' . $this->escape($value2)) . ')';
        if ($this->grouped) {
            $where = '(' . $where;
            $this->grouped = false;
        }
        $this->where = (is_null($this->where))
            ? $where
            : $this->where . ' ' . $andOr . ' ' . $where;
        return $this;
    }

    public function notBetween($field, $value1, $value2)
    {
        $this->between($field, $value1, $value2, 'NOT ', 'AND');
        return $this;
    }

    public function orBetween($field, $value1, $value2)
    {
        $this->between($field, $value1, $value2, '', 'OR');
        return $this;
    }

    public function orNotBetween($field, $value1, $value2)
    {
        $this->between($field, $value1, $value2, 'NOT ', 'OR');
        return $this;
    }

    public function like($field, $data, $type = '', $andOr = 'AND')
    {
        $like = $this->escape($data);
        $where = $field . ' ' . $type . 'LIKE ' . $like;
        if ($this->grouped) {
            $where = '(' . $where;
            $this->grouped = false;
        }
        $this->where = (is_null($this->where))
            ? $where
            : $this->where . ' ' . $andOr . ' ' . $where;
        return $this;
    }

    public function orLike($field, $data)
    {
        $this->like($field, $data, '', 'OR');
        return $this;
    }

    public function notLike($field, $data)
    {
        $this->like($field, $data, 'NOT ', 'AND');
        return $this;
    }

    public function orNotLike($field, $data)
    {
        $this->like($field, $data, 'NOT ', 'OR');
        return $this;
    }

    public function limit($limit, $limitEnd = null)
    {
        $this->limit = (! is_null($limitEnd))
            ? $limit . ', ' . $limitEnd
            : $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function pagination($perPage, $page)
    {
        $this->limit = $perPage;
        $this->offset = (($page > 0 ? $page : 1) - 1) * $perPage;
        return $this;
    }

    public function orderBy($orderBy, $orderDir = null)
    {
        if (! is_null($orderDir)) {
            $this->orderBy = $orderBy . ' ' . strtoupper($orderDir);
        } else {
            $this->orderBy = (stristr($orderBy, ' ') || $orderBy == 'rand()')
                ? $orderBy
                : $orderBy . ' ASC';
        }
        return $this;
    }

    public function groupBy($groupBy)
    {
        $this->groupBy = (is_array($groupBy))
            ? implode(', ', $groupBy)
            : $groupBy;
        return $this;
    }

    public function having($field, $op = null, $val = null)
    {
        if (is_array($op)) {
            $fields = explode('?', $field);
            $where = '';
            foreach ($fields as $key => $value) {
                if (! empty($value)) {
                    $where .= $value . (isset($op[$key]) ? $this->escape($op[$key]) : '');
                }
            }
            $this->having = $where;
        } elseif (! in_array($op, $this->op)) {
            $this->having = $field . ' > ' . $this->escape($op);
        } else {
            $this->having = $field . ' ' . $op . ' ' . $this->escape($val);
        }
        return $this;
    }

    public function numRows()
    {
        return $this->numRows;
    }

    public function insertId()
    {
        return $this->insertId;
    }

    public function error()
    {
        $msg = '<h1>Database Error</h1>';
        $msg .= '<h4>Query: <em style="font-weight:normal;">"' . $this->query . '"</em></h4>';
        $msg .= '<h4>Error: <em style="font-weight:normal;">' . $this->error . '</em></h4>';
        if ($this->debug === true) {
            die($msg);
        }
        throw new PDOException($this->error . '. (' . $this->query . ')');
    }

    public function get($type = null, $argument = null)
    {
        $this->limit = 1;
        $query = $this->getAll(true);
        if ($type === true) {
            return $query;
        }
        return $this->query($query, false, $type, $argument);
    }

    public function getAll($type = null, $argument = null)
    {
        $query = 'SELECT ' . $this->select . ' FROM ' . $this->from;
        if (! is_null($this->join)) {
            $query .= $this->join;
        }
        if (! is_null($this->where)) {
            $query .= ' WHERE ' . $this->where;
        }
        if (! is_null($this->groupBy)) {
            $query .= ' GROUP BY ' . $this->groupBy;
        }
        if (! is_null($this->having)) {
            $query .= ' HAVING ' . $this->having;
        }
        if (! is_null($this->orderBy)) {
            $query .= ' ORDER BY ' . $this->orderBy;
        }
        if (! is_null($this->limit)) {
            $query .= ' LIMIT ' . $this->limit;
        }
        if (! is_null($this->offset)) {
            $query .= ' OFFSET ' . $this->offset;
        }
        if ($type === true) {
            return $query;
        }
        return $this->query($query, true, $type, $argument);
    }

    public function insert(array $data, $type = false)
    {
        $query = 'INSERT INTO ' . $this->from;
        $values = array_values($data);
        if (isset($values[0]) && is_array($values[0])) {
            $column = implode(', ', array_keys($values[0]));
            $query .= ' (' . $column . ') VALUES ';
            foreach ($values as $value) {
                $val = implode(', ', array_map([$this, 'escape'], $value));
                $query .= '(' . $val . '), ';
            }
            $query = trim($query, ', ');
        } else {
            $column = implode(', ', array_keys($data));
            $val = implode(', ', array_map([$this, 'escape'], $data));
            $query .= ' (' . $column . ') VALUES (' . $val . ')';
        }
        if ($type === true) {
            return $query;
        }
        if ($this->query($query, false)) {
            $this->insertId = $this->pdo->lastInsertId();
            return $this->insertId();
        }
        return false;
    }

    public function update(array $data, $type = false)
    {
        $query = 'UPDATE ' . $this->from . ' SET ';
        $values = [];
        foreach ($data as $column => $val) {
            $values[] = $column . '=' . $this->escape($val);
        }
        $query .= implode(',', $values);
        if (! is_null($this->where)) {
            $query .= ' WHERE ' . $this->where;
        }
        if (! is_null($this->orderBy)) {
            $query .= ' ORDER BY ' . $this->orderBy;
        }
        if (! is_null($this->limit)) {
            $query .= ' LIMIT ' . $this->limit;
        }
        if ($type === true) {
            return $query;
        }
        return $this->query($query, false);
    }

    public function delete($type = false)
    {
        $query = 'DELETE FROM ' . $this->from;
        if (! is_null($this->where)) {
            $query .= ' WHERE ' . $this->where;
        }
        if (! is_null($this->orderBy)) {
            $query .= ' ORDER BY ' . $this->orderBy;
        }
        if (! is_null($this->limit)) {
            $query .= ' LIMIT ' . $this->limit;
        }
        if ($query == 'DELETE FROM ' . $this->from) {
            $query = 'TRUNCATE TABLE ' . $this->from;
        }
        if ($type === true) {
            return $query;
        }
        return $this->query($query, false);
    }

    public function analyze()
    {
        return $this->query('ANALYZE TABLE ' . $this->from, false);
    }

    public function check()
    {
        return $this->query('CHECK TABLE ' . $this->from, false);
    }

    public function checksum()
    {
        return $this->query('CHECKSUM TABLE ' . $this->from, false);
    }

    public function optimize()
    {
        return $this->query('OPTIMIZE TABLE ' . $this->from, false);
    }

    public function repair()
    {
        return $this->query('REPAIR TABLE ' . $this->from, false);
    }

    public function transaction()
    {
        if (! $this->transactionCount++) {
            return $this->pdo->beginTransaction();
        }
        $this->pdo->exec('SAVEPOINT trans' . $this->transactionCount);
        return $this->transactionCount >= 0;
    }

    public function commit()
    {
        if (! --$this->transactionCount) {
            return $this->pdo->commit();
        }
        return $this->transactionCount >= 0;
    }

    public function rollBack()
    {
        if (--$this->transactionCount) {
            $this->pdo->exec('ROLLBACK TO trans' . ($this->transactionCount + 1));
            return true;
        }
        return $this->pdo->rollBack();
    }

    public function exec()
    {
        if (is_null($this->query)) {
            return null;
        }
        $query = $this->pdo->exec($this->query);
        if ($query === false) {
            $this->error = $this->pdo->errorInfo()[2];
            $this->error();
        }
        return $query;
    }

    public function fetch($type = null, $argument = null, $all = false)
    {
        if (is_null($this->query)) {
            return null;
        }
        $query = $this->pdo->query($this->query);
        if (! $query) {
            $this->error = $this->pdo->errorInfo()[2];
            $this->error();
        }
        $type = $this->getFetchType($type);
        if ($type === PDO::FETCH_CLASS) {
            $query->setFetchMode($type, $argument);
        } else {
            $query->setFetchMode($type);
        }
        $result = $all ? $query->fetchAll() : $query->fetch();
        $this->numRows = is_array($result) ? count($result) : 1;
        return $result;
    }

    public function fetchAll($type = null, $argument = null)
    {
        return $this->fetch($type, $argument, true);
    }

    public function find($id) {
        return $this->where('id', '=', $id)->get();
    }

    public function query($query, $all = true, $type = null, $argument = null)
    {
        $this->reset();
        if (is_array($all) || func_num_args() === 1) {
            $params = explode('?', $query);
            $newQuery = '';
            foreach ($params as $key => $value) {
                if (! empty($value)) {
                    $newQuery .= $value . (isset($all[$key]) ? $this->escape($all[$key]) : '');
                }
            }
            $this->query = $newQuery;
            return $this;
        }
        $this->query = preg_replace('/\s\s+|\t\t+/', ' ', trim($query));
        $str = false;
        foreach (['select', 'optimize', 'check', 'repair', 'checksum', 'analyze'] as $value) {
            if (stripos($this->query, $value) === 0) {
                $str = true;
                break;
            }
        }
        $type = $this->getFetchType($type);
        $cache = false;
        if (! is_null($this->cache) && $type !== PDO::FETCH_CLASS) {
            $cache = $this->cache->getCache($this->query, $type === PDO::FETCH_ASSOC);
        }
        if (! $cache && $str) {
            $sql = $this->pdo->query($this->query);
            if ($sql) {
                $this->numRows = $sql->rowCount();
                if (($this->numRows > 0)) {
                    if ($type === PDO::FETCH_CLASS) {
                        $sql->setFetchMode($type, $argument);
                    } else {
                        $sql->setFetchMode($type);
                    }
                    $this->result = $all ? $sql->fetchAll() : $sql->fetch();
                }
                if (! is_null($this->cache) && $type !== PDO::FETCH_CLASS) {
                    $this->cache->setCache($this->query, $this->result);
                }
                $this->cache = null;
            } else {
                $this->cache = null;
                $this->error = $this->pdo->errorInfo()[2];
                $this->error();
            }
        } elseif ((! $cache && ! $str) || ($cache && ! $str)) {
            $this->cache = null;
            $this->result = $this->pdo->exec($this->query);
            if ($this->result === false) {
                $this->error = $this->pdo->errorInfo()[2];
                $this->error();
            }
        } else {
            $this->cache = null;
            $this->result = $cache;
            $this->numRows = is_array($this->result) ? count($this->result) : ($this->result == '' ? 0 : 1);
        }
        $this->queryCount++;
        return $this->result;
    }

    public function escape($data)
    {
        return ($data === null) ? 'NULL' : $this->pdo->quote($data);
    }

    public function cache($time)
    {
        $this->cache = new Cache($this->cacheDir, $time);
        return $this;
    }

    public function queryCount()
    {
        return $this->queryCount;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    protected function reset()
    {
        $this->select = '*';
        $this->from = null;
        $this->where = null;
        $this->limit = null;
        $this->offset = null;
        $this->orderBy = null;
        $this->groupBy = null;
        $this->having = null;
        $this->join = null;
        $this->grouped = false;
        $this->numRows = 0;
        $this->insertId = null;
        $this->query = null;
        $this->error = null;
        $this->result = [];
        $this->transactionCount = 0;
    }

    protected function getFetchType($type)
    {
        return $type === 'class'
            ? PDO::FETCH_CLASS
            : ($type === 'array'
                ? PDO::FETCH_ASSOC
                : PDO::FETCH_OBJ);
    }
}