<?php

namespace App\Controllers;

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
}
