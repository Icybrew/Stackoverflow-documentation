<?php

namespace App\Core;

use App\Controllers;

class App {

    public $current_controller = null;

    public function __construct()
    {
        // Initialising route
        $route = new Route();

        // Getting current routes controller name
        $className = "App\\Controllers\\" . $route->currentRoute["controller"];

        // Getting current routes controller method name
        $functionName = $route->currentRoute["function"];

        // Checking if route was found
        if ($route->currentRoute) {

            // Checking if current route controller class described in routes.php ( Autoloaded )
            if (class_exists($className)) {
                $this->current_controller = new $className;

                if (method_exists($this->current_controller, $functionName)) {
                    $this->current_controller->$functionName();
                } else {
                    throw new \Error("Method: $functionName in $className not found!");
                }

            } else {
                throw new \Error("Class: $className not found!");
            }

        } else {
            ($this->current_controller = new Controllers\ErrorController())->index();
        }
    }
}