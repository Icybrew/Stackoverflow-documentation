<?php

namespace App\Controllers;

use App\Core\Config;
use App\Core\DB;
use http\Env\Request;

class TopicController extends controller {

    public function show($id) {
        echo "Topic show - $id";
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

    }
