<?php

namespace App\Core;

class Config {
    public static function get($config, $setting) {
        $path = '../config/' . $config . '.php';

        if (file_exists($path)) {
            $config = (include $path);
            return isset($config[$setting]) ? $config[$setting] : null;
        } else {
            throw new \Error("Config '$config' not found!");
        }
    }
}