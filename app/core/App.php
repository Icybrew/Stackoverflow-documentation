<?php

namespace App\Core;

class App {

    private $current_controller = null;

    public function __construct()
    {
        // Initialising router
        $router = new Router();

        // Getting route controller name
        $className = "App\\Controllers\\" . $router->getRoute()->getController();

        // Getting route function name
        $functionName = $router->getRoute()->getFunction();

        // Checking if route controller class autoloaded
        if (class_exists($className)) {

            // Initialising controller
            $this->current_controller = new $className();

            // Checking if route controller contains function
            if (method_exists($this->current_controller, $functionName)) {

                // Calling route function with parameters
                call_user_func_array([$this->current_controller, $functionName], $router->getRoute()->getParameterValues());

            } else {
                throw new \Error("Method: $functionName in $className not found!");
            }

        } else {
            throw new \Error("Class: $className not found!");
        }
    }
}
