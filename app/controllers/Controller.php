<?php

namespace App\Controllers;

use App\Core\Config;
use App\Core\Router;

class Controller {
    public function __construct()
    {
        // Base controller
    }


    /**
     * @deprecated use view helper function instead
     */
    protected function _view($view, $data = []) {
        $view = $view . '.php.twig';
        $viewPath = '../resources/views/' . $view;
        $twigLoader = new \Twig\Loader\FilesystemLoader('../resources/views');
        $twig = new \Twig\Environment($twigLoader);

        $twig->addFunction(new \Twig\TwigFunction('asset', function ($asset) {
            return sprintf(Config::get('config', 'root') . '%s', rtrim($asset, '/'));
        }));

        $twig->addFunction(new \Twig\TwigFunction('method', function ($method)  {
            $g = '<input type="hidden" name="_method" value="' . $method . '">';
            return new \Twig\Markup($g, 'UTF-8');
        }));


        $twig->addFunction(new \Twig\TwigFunction('route', function ($name, $param = []) {
            $route = Router::findRouteByName($name);
            if (is_null($route)) return null;

            try {
                $url = $route->getUrl($param);
            } catch (\Throwable $ex) {
                return $ex;
            }

            return sprintf(Config::get('config', 'root') . '%s', $url);
        }));

        if (file_exists($viewPath)) {
            $twig->display($view, $data);
        } else {
            throw new \Error("View '$viewPath' not found");
        }
    }
}