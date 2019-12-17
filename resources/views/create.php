<?php
$topicName = $topicErr "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty($_POST["topic-name"])){
        $topicErr = "Topic name is required";
    }
} else{
    $topicname = topic_input($_POST["topic-name"]);
}
