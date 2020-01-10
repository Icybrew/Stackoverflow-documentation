<?php

namespace App\Controllers;

use App\Core\Router;
use App\Examples;
use App\Topic;
use App\Core\Config;
use App\Core\DB;
use Symfony\Component\HttpFoundation\Request;

class ExampleController extends controller
{
    public function index($id)
    {
        $topic = Topic::select('topics.*, doctags.Title AS tag')
            ->join('doctags', 'doctags.Id', '=', 'topics.DocTagId')
            ->where('topics.Id', '=', $id)
            ->where('deleted', '=', 0)
            ->get();

        if (empty($topic)) {
            $this->view("errors/error404");
        } else {
            $examples = Examples::select('*')
                ->where('DocTopicId', '=', $topic->Id)
                ->where('deleted', '=', 0)
                ->getAll();
            $this->view('example/index', ['topic' => $topic, 'examples' => $examples]);
        }
    }
    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function create($id)
    {

    }

    public function store(Request $request)
    {
       
    }

    public function delete($id, Router $router, Request $request)
    {
        $example = Examples::find($id);

        if (empty($example)) {
            $this->view("errors/error404");
        } else {
            $deleted = $example->deleted;
            $topic = Topic::find($id);

            if ($deleted == 0) {
                DB::table('Examples')->where('Id', '=', $id)->update(['deleted' => 1]);

                return redirect()->route('example.index', ['topic', 'topic' => $topic->Id], 'examples');

            } else {
                $this->view("errors/error404");
            }
        }
    }

}
