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
            $this->view("errors/error404");
        }else{
            $this->view('topic/topic', ['topic' => $topic]);
        }
    }

    public function edit($id) {
        {
            $topic = (DB::queryObject("SELECT * FROM topics WHERE id=$id"));
            $docTags = (DB::queryObject("SELECT * FROM doctags"));

            $this->view('topics/editTopic', [
                "topic" => $topic,
                "title" => Config::get('config', 'name'),
                "docTags" => $docTags
            ]);
        }
    }

    public function update($id) {
        echo "Topic update - $id";
        var_dump($_POST);
    }

    public function create() {
        $doctags = DB::table("doctags")->select(['Id', 'Title'])->orderBy('Title ASC')->getAll();
        $this->view('topic/create', ["doctags" => $doctags]);
    }

    public function store() {
        var_dump($_POST);
    }
}
