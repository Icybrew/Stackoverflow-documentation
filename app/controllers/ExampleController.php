<?php

namespace App\Controllers;

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
            return view("errors/error404");
        } else {
            $examples = Examples::select('*')
                ->where('DocTopicId', '=', $topic->Id)
                ->where('deleted', '=', 0)
                ->getAll();
            $this->view('example/index', ['topic' => $topic, 'examples' => $examples]);
            $examples = Examples::select('*')->where('DocTopicId', '=', $topic->Id)->getAll();
            return view('example/index', ['topic' => $topic, 'examples' => $examples]);
        }
    }
    public function show($id)
    {
        var_dump($id);
    }

    public function edit($id)
    {
        $example = Examples::find($id);

        if (empty($example)) {
            return view("errors/error404");
        } else {
            return view('example/edit', [
                "example" => $example,
                "title" => Config::get('config', 'name'),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->request->all();

        if (!isset($data['Title']) || !isset($data['BodyHtml'])) {
            return view('errors/error404');
        } else {
            $query = [
                "Title" => $data['Title'],
                "BodyHtml" => $data['BodyHtml'],
            ];

            if (strlen($query['Title']) <= 0 || strlen($query['BodyHtml']) <= 0) {
                return view('errors/error404');
            }

            $docTopicId = Examples::find($id);
            $docTopicId = $docTopicId->{'DocTopicId'};

            Examples::update($query, $id);

            return redirect()->route('example.show', ['topic' => $docTopicId, 'example' => $id]);
        }
    }

    public function create($id)
    {
        $topic=Topic::find($id);
        return view('example/create', ['topic'=>$topic]);
    }

    public function store(Request $request, $id)
    {
        $data = $request->request->all();
        if(!isset($data['title']) || !isset($data['bodyhtml'])){
            return view('errors/error404');
        } else {
            $query = [
                "DocTopicId" => $id,
                "Title" => $data['title'],
                "BodyHtml" => $data['bodyhtml'],
                "BodyMarkdown" => strip_tags($data['bodyhtml'])
            ];

            if(strlen($query['Title']) <= 0 || strlen($query['BodyHtml']) <= 0){
                return view('errors/error404');
            }


            $exampleId = Examples::insert($query);
            return redirect()->route('example.show', ['topic' => $id, 'example' => $exampleId]);

        }
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
