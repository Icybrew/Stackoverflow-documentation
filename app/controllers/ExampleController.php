<?php

namespace App\Controllers;

use App\Examples;
use App\Topic;
use App\Core\Config;
use App\Core\DB;
use Symfony\Component\HttpFoundation\Request;

class ExampleController extends controller
{
    public function index()
    {

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
        $topic=Topic::find($id);
        $this->view('example/create', ['topic'=>$topic]);
    }

    public function store(Request $request, $id)
    {
        $data = $request->request->all();
        if(!isset($data['title']) || !isset($data['bodyhtml'])){
            $this->view('errors/error404');
        } else {
            $query = [
                "DocTopicId" => $id,
                "Title" => $data['title'],
                "BodyHtml" => $data['bodyhtml'],
                "BodyMarkdown" => strip_tags($data['bodyhtml'])
            ];

            if(strlen($query['Title']) <= 0 || strlen($query['BodyHtml']) <= 0){
                $this->view('errors/error404');
            }


            $id = Examples::insert($query);
            $hostname = 'http://' . $request->server->get('HTTP_HOST');
            $uri = $request->server->get('REQUEST_URI');
            $redirect = $hostname . $uri . '/' . $id;
            header("Location: $redirect");

        }
    }

    public function delete($id)
    {

    }
}
