<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\Request;

class App {

    private $current_controller = null;

    public function __construct()
    {
        // Initialising router
        $router = new Router();

        // Initialising HttpFoundation
        $request = Request::createFromGlobals();

        $dependencies = [
            'App\Core\App' => $this,
            'App\Core\Router' => $router,
            'Symfony\Component\HttpFoundation\Request' => $request
        ];

        // Getting route controller name
        $className = "App\\Controllers\\" . $router->getRoute()->getController();

        // Getting route function name
        $functionName = $router->getRoute()->getFunction();

        // Checking if route controller class autoloaded
        if (class_exists($className)) {

            // Initialising controller
            $this->current_controller = new $className();

            $function = new \ReflectionMethod($className, $functionName);
            $routeParameters = $router->getRoute()->getParameterValues();
            $parameters = [];

            // Injecting dependencies and parameters
            foreach ($function->getParameters() as $parameter) {
                $type = $parameter->getType();

                if (isset($type)) {
                    $name = $type->getName();
                    $parameters[] = $dependencies[$name];
                } else {
                    if (empty($routeParameters)) continue;
                    $parameters[] = array_pop($routeParameters);
                }

            }

            // Calling route function with injected parameters
            call_user_func_array([$this->current_controller, $functionName], $parameters);

        } else {
            throw new \Error("Class: $className not found!");
        }
    }
}
