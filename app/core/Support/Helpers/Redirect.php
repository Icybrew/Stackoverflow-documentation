<?php


namespace App\Core\Support\Helpers;


use App\Core\Config;
use App\Core\Router;
use App\Core\Support\Interfaces\Renderable;

class Redirect implements Renderable
{
    private $to;

    public function __construct(string $to = null)
    {
        $this->to = $this->getBaseUrl($to);
    }

    public function render()
    {
        header("location: $this->to");
    }

    public function route(string $name, array $parameters = []) : ?Redirect
    {
        $route = Router::findRouteByName($name);

        if (isset($route)) {
            $url = $route->getUrl($parameters);

            $this->to = $this->getBaseUrl($url);
        } else {
            throw new \Exception("Route '$name' not found!");
        }
        return $this;
    }

    public function getBaseUrl($url) {
        return "http://$_SERVER[HTTP_HOST]" . Config::get('config', 'root') . $url;
    }

}