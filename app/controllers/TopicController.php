<?php

namespace App\Controllers;

use App\Topic;
use App\Core\DB;
use App\Core\Config;

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
            $topic = Topic::find($id);
            $docTags = DB::table("doctags")->getAll();

            if(empty($topic)){
                $this->view("errors/error404");
            }else{
                $this->view('topic/edit', [
                    "topic" => $topic,
                    "title" => Config::get('config', 'name'),
                    "docTags" => $docTags
                ]);
            }
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
