<?php

namespace App\Core;

class Route {
    public static $ROUTES = [];

    public $url = null;
    public $currentRoute = null;

    public function __construct()
    {
        // Loading routes
        require_once '../config/routes.php';

        // Processing url
        $this->url = $this->getUrl();

        // Checking if loaded routes contain given url
        if (isset(self::$ROUTES[$this->url])) {
            $this->currentRoute = self::$ROUTES[$this->url];
        }
    }

    private static function addRoute($url, $controller, $method) {
        $exploded = explode("@", $controller);

        if (strlen($url) > 1) {
            $url = ltrim($url, "/");
            $url = rtrim($url, "/");
        }

        self::$ROUTES[$url] = [
            "controller" => $exploded[0],
            "function" => $exploded[1],
            "method" => $method
        ];
    }

    public static function get($url, $controller) {
        self::addRoute($url, $controller, 'get');
    }

    public static function post($url, $controller) {
        self::addRoute($url, $controller, 'post');
    }

    private function getUrl(){
        if(isset($_GET['url'])){
            $url = filter_var($_GET['url'], FILTER_SANITIZE_URL);
            $url = rtrim($url, '/');

            return $url;
        } else {
            return '/';
        }
    }
}