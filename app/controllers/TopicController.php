<?php

namespace App\Controllers;

use http\Env\Request;

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

    }
}
