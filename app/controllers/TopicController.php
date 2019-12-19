<?php

namespace App\Controllers;

use http\Env\Request;
use App\Core\DB;

class TopicController extends controller {

    public function show($id) {
        echo "Topic show - $id";
    }

    public function edit($id) {
        echo "Topic edit - $id";
    }

    public function update($id) {
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
