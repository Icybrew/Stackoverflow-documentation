<?php

use App\Core\Router;

/* Index */
Router::get("", "indexController@index")->name('home');

/* Topic  */

Router::get("/topic", "IndexController@index")->name('topic.index');
Router::get("/topic/create", "TopicController@create")->name('topic.create');
Router::get("/topic/search", "TopicController@search")->name('topic.search');
Router::get("/topic/{topic}", "TopicController@show")->name('topic.show');
Router::get("/topic/{topic}/edit", "TopicController@edit")->name('topic.edit');
Router::patch("/topic/{topic}", "TopicController@update")->name('topic.update');
Router::delete("/topic/{topic}", "TopicController@delete")->name('topic.delete');
Router::put("/topic", "TopicController@store")->name('topic.store');
Router::get("/topic/{topic}/examples", "ExampleController@index")->name('example.index');
Router::get("/topic/{topic}/examples/create", "ExampleController@create")->name('example.create');
Router::get("/topic/{topic}/examples/{example}", "ExampleController@show")->name('example.show');
Router::get("/topic/{topic}/examples/{example}/edit", "ExampleController@edit")->name('example.edit');
Router::patch("/topic/{topic}/examples/{example}", "ExampleController@update")->name('example.update');
Router::delete("/topic/{topic}/examples/{example}", "ExampleController@delete")->name('example.delete');
Router::put("/topi/{topic}/examples", "ExampleController@store")->name('example.store');



/* API */

/* DocTags */
Router::get('/api/doctags', 'DocTagsController@doctags')->name('api.doctags.doctags');
