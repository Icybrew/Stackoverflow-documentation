<?php

namespace App\Core;

use App\Core\libs\Route;
use App\Core\libs\URL;

class Router {
    private static $_ROUTES = [];

    private $_url = null;
    private $_current_route;

    public function __construct()
    {
        // Importing routes
        require_once '../config/routes.php';

        // Processing URL
        $this->_url = new URL();

        // Finding route by URL
        $route = $this->findRouteByUrl($this->_url);

        $this->_current_route = !is_null($route) ? $route : new Route($_SERVER["REQUEST_METHOD"], null, 'ErrorController', 'index');
    }

    public function getUrl() {
        return $this->_url;
    }

    public function getRoutes() {
        return self::$_ROUTES;
    }

    public function getRoute() {
        return $this->_current_route;
    }

    public function findRouteByUrl($url) {

        // Searching through routes for matching url
        foreach (self::$_ROUTES as $route) {
            if ($url->getMethod() !== $route->getMethod()) continue;

            $routeUrl = $route->getUrl();

            // Regexify
            $routeUrl = preg_replace('#/#', '\/', $routeUrl);
            $routeUrl = preg_replace('#\{[A-z]+\}#', '([^\/])+', $routeUrl);

            if(preg_match("#^\/?" . $routeUrl ."\/?$#", $url->getUrl())) {
                $route->setParamsByUrl($url);
                return $route;
            }
        }
        return null;
    }

    private static function addRoute($url, $controller, $method) {
        // Get controller as index 0 and function as index 1
        $controller = explode("@", $controller);

        $params = [];

        // Trim unnecessary slashes
        if (strlen($url) > 1) {
            $url = ltrim($url, "/");
            $url = rtrim($url, "/");
        }

        // Explode for easy management
        $url = explode('/', $url);

        // Check for parameters
        foreach ($url as $key => $val) {
            if (preg_match("/^{[A-z0-9]+}$/", $val)) {
                $name = str_replace(['{', '}'], '', $val);
                $params[$name] = [
                    'index' => $key,
                    'value' => null
                ];
            }
        }

        // Glue back together
        $url = implode('/', $url);

        self::$_ROUTES[] = new Route($method, $url, $controller[0], $controller[1], $params);
    }

    public static function get($url, $controller) {
        self::addRoute($url, $controller, 'GET');
    }

    public static function post($url, $controller) {
        self::addRoute($url, $controller, 'POST');
    }
}
