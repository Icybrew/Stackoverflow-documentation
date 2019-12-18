<?php

namespace App\Controllers;

use App\Core\Config;

class Controller {
    public function __construct()
    {
        // Base controller
    }

    protected function view($view, $data = []) {
        $view = $view . '.php.twig';
        $viewPath = '../resources/views/' . $view;
        $twigLoader = new \Twig\Loader\FilesystemLoader('../resources/views');
        $twig = new \Twig\Environment($twigLoader);

        $twig->addFunction(new \Twig\TwigFunction('asset', function ($asset) {
            return sprintf(Config::get('config', 'root') . '%s', rtrim($asset, '/'));
        }));

        if (file_exists($viewPath)) {
            $twig->display($view, $data);
        } else {
            throw new \Error("View '$viewPath' not found");
        }
    }
}