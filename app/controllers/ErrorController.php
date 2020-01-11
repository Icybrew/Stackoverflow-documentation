<?php

namespace App\Controllers;

use App\Examples;

class ErrorController extends Controller {

    public function index() {
        return view("errors/error404");
    }
}
