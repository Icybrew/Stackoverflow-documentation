<?php

namespace App\Core;

use PDO;

abstract class Model extends DB
{
    protected static $table;

    protected static $primary_key = 'id';

    public function __construct()
    {
        // Base Model
    }

    private static function getTable() {
        return (static::$table != null) ? static::$table : (new \ReflectionClass(get_called_class()))->getShortName();
    }

    private static function getPrimaryKey() {
        return (static::$primary_key) ? static::$primary_key : self::$primary_key;
    }

    public static function find($id) {
        $table = self::getTable();
        $key = self::getPrimaryKey();
        $result = self::queryObject("SELECT * FROM $table WHERE $key = $id");
        return $result;
    }

    public static function where($column, $value) {
        $table = self::getTable();
        $result = self::queryObject("SELECT * FROM $table WHERE $column = '$value'");
        return $result;
    }
}
