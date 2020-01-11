<?php

namespace App\Controllers;

use App\Core\Router;
use App\Doctag;
use App\Examples;
use App\Topic;
use App\Core\Config;
use App\Core\DB;
use Symfony\Component\HttpFoundation\Request;

class TopicController extends controller
{
    public function show($id)
    {
        $topic = Topic::select('topics.*, doctags.Title AS tag')
            ->join('doctags', 'doctags.Id', '=', 'topics.DocTagId')
            ->where('topics.Id', '=', $id)
            ->where('deleted', '=', 0)
            ->get();

        if (empty($topic)) {
            return view("errors/error404");
        } else {
            $examples = Examples::select('*')->where('DocTopicId', '=', $topic->Id)->getAll();
            return view('topic/topic', ['topic' => $topic, 'examples' => $examples]);
        }
    }

    public function edit($id)
    {
        $topic = Topic::find($id);
        $docTags = Doctag::all();

        if (empty($topic)) {
            return view("errors/error404");
        } else {
            return view('topic/edit', [
                "topic" => $topic,
                "title" => Config::get('config', 'name'),
                "docTags" => $docTags
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->request->all();
        $topic = Topic::find($id);

        if (!isset($topic, $data['topicTitle'], $data['topicDocTag'], $data['RemarksHtml']) || strlen($data['topicTitle']) == 0 || strlen($data['topicDocTag']) == 0 || strlen($data['RemarksHtml']) == 0) {
            return view('errors/error404');
        }

        $query = [
            "Title" => $data['topicTitle'],
            "DocTagId" => $data['topicDocTag'],
            "RemarksHtml" => $data['RemarksHtml'],
        ];
        Topic::update($query, $id);

        return redirect()->route('topic.show', ['topic' => $topic->Id]);
    }

    public function create()
    {
        $doctags = Doctag::select(['Id', 'Title'])->orderBy('Title', 'ASC')->getAll();
        return view('topic/create', ["doctags" => $doctags]);
    }

    public function store(Request $request)
    {
        $data = $request->request->all();

        if (!isset($data['title']) || !isset($data['doctag']) || !isset($data['content'])) {
            return view('errors/error404');
        } else {
            $query = [
                "Title" => $data['title'],
                "DocTagId" => $data['doctag'],
                "RemarksHtml" => $data['content'],
            ];

            if (strlen($query['Title']) <= 0 || strlen($query['DocTagId']) <= 0 || strlen($query['RemarksHtml']) <= 0) {
                return view('errors/error404');
            }

            $id = Topic::insert($query);

            return redirect()->route('topic.show', ['topic' => $id]);
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
            $topics = Topic::select('topics.*, doctags.tag as tag')->join('doctags', 'doctags.Id', '=', 'topics.DocTagId')->where('topics.title', 'LIKE', "%$search%")->where('deleted', '=', 0)->groupBy('topics.Id')->pagination($perPage, $page);
            if (!empty($docTag)) {
                $topics->where('DocTagId', '=', $docTag->Id);
            }
            $topics = $topics->getAll();

            $topicCount = Topic::select('COUNT(topics.Id) AS count')->join('doctags', 'doctags.Id', '=', 'topics.DocTagId')->where('topics.title', 'LIKE', "%$search%")->where('deleted', '=', 0);

            if (!empty($docTag)) {
                $topicCount->where('DocTagId', '=', $docTag->Id);
            }

            $topicCount = $topicCount->get();
        }
        if ($page < 0 || $perPage * $page > ceil(($topicCount->count / $perPage)) * $perPage) {
            return view('errors/error404');
        } else {
            return view('index', ['topics' => $topics, "title" => Config::get('config', 'name'), 'category' => $category, 'search' => $search, 'page' => $page, 'pageCount' => ceil($topicCount->count / $perPage)]);
        }
    }

    public function delete($id, Router $router, Request $request)
    {
        $topic = Topic::find($id);

        if (empty($topic)) {
            return view("errors/error404");
        } else {
            $deleted = $topic->deleted;

            if ($deleted == 0) {
                DB::table('Topics')->where('Id', '=', $id)->update(['deleted' => 1]);

                return redirect()->route('home');
            } else {
                return view("errors/error404");
            }
        }
    }
}
