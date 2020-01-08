<?php

namespace App\Controllers;

use App\Core\Config;
use App\Topic;

class indexController extends Controller
{

    public function __construct()
    {
        // Home Controller
    }

    public function index()
    {
        
        $topics = Topic::select('*')->where('deleted', '=', '0')->limit(10)->getAll();
        $this->view('index', ["topics" => $topics, "title" => Config::get('config', 'name')]);
    }
}
