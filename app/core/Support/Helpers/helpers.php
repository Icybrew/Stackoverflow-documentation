<?php

function redirect(string $to = null) {
    return new \App\Core\Support\Helpers\Redirect($to);
}

function view(string $name, array $data = []) {
    return new \App\Core\Support\Helpers\View($name, $data);
}