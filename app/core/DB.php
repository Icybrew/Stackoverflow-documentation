<?php

namespace App\Core;

use PDO;

class DB {

    private static $_PDO;
    private static $_CONNECTED = false;

    private static $_QUERY;
    public static $ERROR;

    private static function connect() {
        if (self::$_CONNECTED) return;

        $hostname = Config::get('database', 'hostname');
        $username = Config::get('database', 'username');
        $password = Config::get('database', 'password');
        $database = Config::get('database', 'database');

        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        $dsn = "mysql:host=$hostname;dbname=$database";

        try {
            self::$_PDO = new PDO($dsn, $username, $password, $options);
        } catch (\PDOException $exception) {
            self::$ERROR = $exception->getMessage();
            echo self::$ERROR;
        }

        self::$_CONNECTED = true;
    }

    public static function queryRaw($query) {
        self::connect();
        $statement = self::$_PDO->query($query);
        return $statement;
    }

    public static function queryObject($query) {
        self::connect();
        $statement = self::$_PDO->query($query);
        if ($statement->rowCount() <= 1) {
            return $statement->fetchObject();
        } else {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        }
    }

    public static function queryArray($query) {
        self::connect();
        $statement = self::$_PDO->query($query);
        if ($statement->rowCount() <= 1) {
            return $statement->fetch();
        } else {
            return $statement->fetchAll();
        }
    }
}
