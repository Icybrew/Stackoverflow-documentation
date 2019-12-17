<?php

namespace App\Controllers;

use App\Core\Config;
use App\Core\DB;

class indexController extends Controller {

    public function __construct()
    {
        // Home Controller
    }

    public function index() {
        $topics = (DB::queryObject("SELECT * FROM topics LIMIT 10"));
        $this->view('index', ["topics" => $topics, "title" => Config::get('config', 'name')]);
    }

}
