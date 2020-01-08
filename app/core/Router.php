<?php

namespace App\Core;

use App\Core\Libs\Route;
use App\Core\Libs\URL;

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

        $this->_current_route = !is_null($route) ? $route : new Route($_SERVER["REQUEST_METHOD"], $this->_url->getUrl(), 'ErrorController', 'index');
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

        // Searching for matching route by URL
        foreach (self::$_ROUTES as $route) {

            // Skip if request method doesn't match
            if ($url->getMethod() !== $route->getMethod()) continue;

            // Testing if url matches
            if(preg_match("#^" . $route->getUrlRegex() ."$#", $url->getUrl())) {

                // Setting route variables
                $route->setParametersByIndex(explode('/', $url->getUrl()));

                // Returning matched route for chaining
                return $route;
            }
        }

        // No matching route
        return null;
    }

    public static function findRouteByName($name) : ?Route {
        $find = null;

        array_filter(self::$_ROUTES, function ($route) use ($name, &$find) {
            if ($route->getName() === $name) {
                $find = $route;
            }
        });
        return $find;
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

        $route = new Route($method, $url, $controller[0], $controller[1], $params);

        self::$_ROUTES[] = $route;

        return $route;
    }

    public static function get($url, $controller) {
        return self::addRoute($url, $controller, 'GET');
    }

    public static function post($url, $controller) {
        return self::addRoute($url, $controller, 'POST');
    }

    public static function patch($url, $controller) {
        return self::addRoute($url, $controller, 'PATCH');
    }

    public static function put($url, $controller) {
        return self::addRoute($url, $controller, 'PUT');
    }

    public static function delete($url, $controller) {
        return self::addRoute($url, $controller, 'DELETE');
    }
}
