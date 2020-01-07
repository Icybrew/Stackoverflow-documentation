<?php

use App\Core\Router;

/* Index */
Router::get("/", "indexController@index")->name('home');

/* Topic  */

Router::get("/topic", "IndexController@index")->name('topic.index');
Router::get("/topic/create", "TopicController@create")->name('topic.create');
Router::get("/topic/search", "TopicController@search")->name('topic.search');
Router::get("/topic/{topic}", "TopicController@show")->name('topic.show');
Router::get("/topic/{topic}/edit", "TopicController@edit")->name('topic.edit');
Router::patch("/topic/{topic}", "TopicController@update")->name('topic.update');
Router::delete("/topic/{topic}", "TopicController@delete")->name('topic.delete');
Router::put("/topic", "TopicController@store")->name('topic.store');
