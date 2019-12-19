<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected static $table;

    protected static $primary_key = 'id';

    public function __construct()
    {
        // Base Model
    }

    public static function getTable() {
        return (static::$table != null) ? static::$table : (new \ReflectionClass(get_called_class()))->getShortName();
    }

    private static function getPrimaryKey() {
        return (static::$primary_key) ? static::$primary_key : self::$primary_key;
    }

    private static function createBuilder($table) {
        return DB::table($table);
    }

    public static function find($id) {
        return self::createBuilder(self::getTable())->find($id);
    }

    public static function select($fields) {
        return self::createBuilder(self::getTable())->select($fields);
    }

    public static function where($where, $op = null, $val = null, $type = '', $andOr = 'AND') {
        return self::createBuilder(self::getTable())->select($where, $op, $val, $type, $andOr);
    }

    public function all($type = null, $argument = null) {
        return self::createBuilder(self::getTable())->getAll($type, $argument);
    }
}
