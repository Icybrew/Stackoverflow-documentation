<?php

namespace App\Controllers;

use App\Topic;
use App\Core\DB;

class TopicController extends controller
{
    public function show($id)
    {
        $topic = Topic::find($id);
        if(!isset($topic)){
            $topics = (DB::queryObject("SELECT * FROM topics LIMIT 10"));
            $this->view('index', ["topics" => $topics]);
        }else{
            $this->view('topic/topic', ['topic' => $topic]);
        }
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
