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

    public function create()
    {
        $this->view('example/create');
    }

    public function store(Request $request)
    {

    }

    public function delete($id)
    {

    }
}
