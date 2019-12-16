<?php

use App\Core\Router;

/* Index */
Router::get("/", "indexController@index");

/* Topic  */
Router::get("/topic", "IndexController@index");
Router::get("/topic/{topic}", "TopicController@show");
Router::get("/topic/{topic}/edit", "TopicController@edit");
Router::post("/topic/{topic}", "topicController@update");
