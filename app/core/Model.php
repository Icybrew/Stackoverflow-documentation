<?php

namespace App\Core;

abstract class Model
{
    protected static $table;

    protected static $primary_key = 'id';

    public function __construct()
    {
        // Base Model
    }

    public static function getTable()
    {
        return (static::$table != null) ? static::$table : (new \ReflectionClass(get_called_class()))->getShortName();
    }

    private static function getPrimaryKey()
    {
        return (static::$primary_key) ? static::$primary_key : self::$primary_key;
    }

    private static function createBuilder($table)
    {
        return DB::table($table);
    }

    public static function find($id)
    {
        return self::createBuilder(self::getTable())->where('deleted', '=', 0)->find($id);
    }

    public static function select($fields)
    {
        return self::createBuilder(self::getTable())->select($fields);
    }

    public static function where($where, $op = null, $val = null, $type = '', $andOr = 'AND')
    {
        return self::createBuilder(self::getTable())->where($where, $op, $val, $type, $andOr);
    }

    public static function all($type = null, $argument = null)
    {
        return self::createBuilder(self::getTable())->getAll($type, $argument);
    }

    public static function update(array $data, $id)
    {
        return self::createBuilder(self::getTable())->where('id', '=', $id)->update($data);
    }

    public static function insert(array $data, $type = false)
    {
        return self::createBuilder(self::getTable())->insert($data, $type);

    }
}
