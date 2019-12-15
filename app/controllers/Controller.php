<?php

namespace App\Controllers;

class Controller {
    public function __construct()
    {
        // Base controller
    }

    protected function view($view, $data = null) {
        $view = $view . '.php';
        $viewPath = '../resources/views/' . $view;
        $twigLoader = new \Twig\Loader\FilesystemLoader('../resources/views');
        $twig = new \Twig\Environment($twigLoader);

        if (file_exists($viewPath)) {
            $twig->display($view, $data);
        } else {
            throw new \Error("View '$viewPath' not found");
        }
    }
}