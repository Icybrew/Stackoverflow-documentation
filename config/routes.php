<?php

use App\Core\Router;

/* Index */
Router::get("/", "indexController@index")->name('home');

/* Topic  */
Router::get("/topic", "IndexController@index")->name('topic.index');
Router::get("/topic/{topic}", "TopicController@show")->name('topic.show');
Router::get("/topic/{topic}/edit", "TopicController@edit")->name('topic.edit');
Router::post("/topic/{topic}", "topicController@update")->name('topic.update');

