<?php

namespace App\Controllers;

class Controller {
    public function __construct()
    {
        // Base controller
    }

    protected function view($view, $data = null) {
        $viewPath = '../resources/views/' . $view . '.php';

        if (file_exists($viewPath)) {
            require_once($viewPath);
        } else {
            throw new \Error("View '$viewPath' not found");
        }
    }
}