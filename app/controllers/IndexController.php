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
        //var_dump($topics);
        $this->view('index', ["topics" => $topics]);
    }

}