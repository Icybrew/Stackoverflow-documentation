<?php

namespace App\Controllers;

use App\Doctag;
use App\Topic;
use App\Core\Config;
use Symfony\Component\HttpFoundation\Request;

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

    public function store(Request $request)
    {
        $data = $request->request->all();

        if(!isset($data['title']) || !isset($data['doctag']) || !isset($data['content'])){
            $this->view('errors/error404');
        } else {
            $query = [
                "Title" => $data['title'],
                "DocTagId" => $data['doctag'],
                "RemarksHtml" => $data['content'],
            ];

            if(strlen($query['Title']) <= 0 || strlen($query['DocTagId']) <= 0 || strlen($query['RemarksHtml']) <= 0){
                $this->view('errors/error404');
            }

            $id = Topic::insert($query);
            $hostname = 'http://' . $request->server->get('HTTP_HOST');
            $uri = $request->server->get('REQUEST_URI');
            $redirect = $hostname . $uri . '/' . $id;
            header("Location: $redirect");

        }
    }

    public function search()
    {
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : null;
        $perPage = 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        if (isset($category)) {
            $docTag = Doctag::select('Id')->where('Tag', '=', $category)->get();
        }

        if ($page >= 0) {
            $topics = Topic::select('topics.*, doctags.tag as tag')->join('doctags', 'doctags.Id', '=', 'topics.DocTagId')->where('topics.title', 'LIKE', "%$search%")->where('deleted', '=', 0)->pagination($perPage, $page);
            if (!empty($docTag)) {
                $topics->where('DocTagId', '=', $docTag->Id);
            }
            $topics = $topics->getAll();

            $topicCount = count($topics);
        }
        if ($page < 0 || $perPage * $page > ceil(($topicCount / $perPage)) * $perPage) {
            $this->view('errors/error404');
        } else {
            $this->view('index', ['topics' => $topics, "title" => Config::get('config', 'name'), 'category' => $category, 'search' => $search, 'page' => $page, 'topicCount' => ($topicCount / $perPage)]);
        }
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
