<?php

namespace App\Core;

use App\Core\Support\Interfaces\Renderable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class App {

    private $current_controller = null;

    public function __construct()
    {
        include 'Support\Helpers\helpers.php';

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
            $response = call_user_func_array([$this->current_controller, $functionName], $parameters);

            if (isset($response)) {
                if ($response instanceof Renderable) {
                    $response->render();
                } else {
                    $res = Response::create($response);
                    $res->send();
                }
            }

        } else {
            throw new \Error("Class: $className not found!");
        }
    }
}
