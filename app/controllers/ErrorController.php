<?php

namespace App\Controllers;

use App\Examples;

class ErrorController extends Controller {

    public function index() {
        $this->view("errors/error404");
    }
}
