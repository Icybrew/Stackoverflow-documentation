<?php

namespace App\Core\libs;

class Route {
    private $method;
    private $url;
    private $controller;
    private $function;
    private $params;

    public function __construct($method, $url, $controller, $function, $params = [])
    {
        $this->method = $method;
        $this->url = $url;
        $this->controller = $controller;
        $this->function = $function;
        $this->params = $params;
    }

    public function setParamsByUrl($url) {
        foreach ($this->params as &$param) {
            $param['value'] = $url->getParam($param['index']);
        }
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getController() {
        return $this->controller;
    }

    public function getFunction() {
        return $this->function;
    }

    public function getParams() {
        return $this->params;
    }

    public function getParameterValues() {
        $values = [];
        foreach ($this->params as $parameter) {
            $values[] = $parameter['value'];
        }
        return $values;
    }

    public function compareTo($other) {
        return $this->__toString() === $other->__toString();
    }

    public function __toString()
    {
        return serialize($this);
    }
}