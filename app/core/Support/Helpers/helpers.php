<?php

function redirect(string $to = null) {
    return new \App\Core\Support\Helpers\Redirect($to);
}