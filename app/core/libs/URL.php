<?php

namespace App\Core\Libs;

class URL
{
    private $_method;
    private $_url_unprocessed;
    private $_url_processed;
    private $_url_exploded;

    public function __construct()
    {
        $this->_method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
        $this->_url_unprocessed = isset($_GET['url']) ? $_GET['url'] : null;
        $this->_url_processed = $this->processURL();
        $this->_url_exploded = explode('/', $this->_url_processed);
    }

    public function getMethod()
    {
        return strtoupper($this->_method);
    }

    public function getUrlRaw() {
        return $this->_url_unprocessed;
    }

    public function getUrl() {
        return $this->_url_processed;
    }

    public function getParam($index) {
        return isset($this->_url_exploded[$index]) ? $this->_url_exploded[$index] : null;
    }

    private function processURL() {
        $url = $this->_url_unprocessed;

        if(isset($url)){
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = rtrim($url, '/');

            return $url;
        } else {
            return '/';
        }
    }
}
