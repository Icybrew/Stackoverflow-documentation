<?php

namespace App\Controllers;

use App\Core\Config;

class indexController extends Controller {

    public function __construct()
    {
        // Home Controller
    }

    public function index() {
        $this->view('index', ["title" => Config::get("config", 'name')]);
    }

}