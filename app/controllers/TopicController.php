<?php

namespace App\Controllers;

use App\Doctag;
use App\Topic;

class TopicController extends controller
{
    public function show($id)
    {
        $topic = Topic::find($id);
        if (!isset($topic)) {
            $this->view("errors/error404");
        } else {
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

    public function create()
    {
        $doctags = Doctag::select(['Id', 'Title'])->orderBy('Title', 'ASC')->getAll();
        $this->view('topic/create', ["doctags" => $doctags]);
    }

    public function store()
    {
        var_dump($_POST);
    }
}
