<?php

namespace App\Controllers;

use App\Doctag;
use App\Topic;
use App\Core\Config;
use App\Core\DB;

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
        {
            $topic = Topic::find($id);
            $docTags = Doctag::all();

            if (empty($topic)) {
                $this->view("errors/error404");
            } else {
                $this->view('topic/edit', [
                    "topic" => $topic,
                    "title" => Config::get('config', 'name'),
                    "docTags" => $docTags
                ]);
            }
        }
    }

    public function update($id)
    {
        echo "Topic update - $id";
        var_dump($_POST);
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

    public function delete($id)
    {
        $topic = Topic::find($id);

        if (!isset($topic)) {
            $this->view("errors/error404");
        } else {
            $deleted = $topic->deleted;

            if ($deleted == 0) {
                DB::table('Topics')->where('Id', '=', $id)->update(['deleted' => 1]);
                echo "<script type='text/javascript'>alert('Documentation record deleted');</script>";
            } else {
                $this->view("errors/error404");
            }
        }
    }
}

