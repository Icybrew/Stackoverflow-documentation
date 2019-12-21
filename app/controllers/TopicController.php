<?php

namespace App\Controllers;

use http\Env\Request;

use App\Core\DB;

class TopicController extends controller
{
    public function show($id)
    {
        $topics = DB::table("topics")->where('Id','=', $id)->getAll();
        $this->view('topic/topic', ["topics" => $topics]);
    }

    public function edit($id)
    {
        echo "Topic edit - $id";
    }

    public function update($id)
    {
        echo "Topic update - $id";
    }

    public function create() {
        $doctags = DB::table("doctags")->select(['Id', 'Title'])->orderBy('Title ASC')->getAll();
        $this->view('topic/create', ["doctags" => $doctags]);
    }

    public function store() {
        var_dump($_POST);
    }
}
