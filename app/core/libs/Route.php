<?php

namespace App\Core\Libs;

class Route {

    private $name;
    private $method;
    private $url;
    private $controller;
    private $function;
    private $parameters;

    /**
     * Route constructor.
     * @param string $method
     * @param string $url
     * @param string $controller
     * @param string $function
     * @param array $params
     */
    public function __construct(string $method, string $url, string $controller, string $function, array $params = [], string $name = null)
    {
        $this->method = $method;
        $this->url = $url;
        $this->controller = $controller;
        $this->function = $function;
        $this->parameters = $params;
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }


    /**
     * @param array $parameters
     * @return string
     */
    public function getUrl(array $parameters = []) : string
    {
        $expected = count($this->parameters);
        $got = count($parameters);
        $url = $this->url;

        // Checking if required amount of parameters are given
        if ($expected > $got) throw new \Error(sprintf("Not enough parameters given for route '$this->name', expected $expected, but got $got"));

        foreach ($this->parameters as $name => $parameter) {

            // Throw error if required parameter not given
            if (!isset($parameters[$name])) throw new \Error("No value given for parameter: '$name'");

            // Replace url {name} with supplied parameter
            $url = preg_replace('#\{' . $name . '\}#', $parameters[$name], $url);

            unset($parameters[$name]);
        }


        if (count($parameters) != 0) {

            $url .= '?';

            // Appending rest as GET parameters
            foreach ($parameters as $name => $parameter) {
                $url .= $name . '=' . $parameter . '&';
            }

            $url = rtrim($url, '&');
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getUrlRegex() : string
    {
        $url = $this->url;

        // Placing '/' in front of every special character
        $url = preg_quote($url, '/');

        // Replacing \{Custom words\} to match {ANYTHING}
        $url = preg_replace('#\\\{[A-z]+\\\}#', '[^\/]+', $url);

        // Placing optional '/' in the beginning and in the end
        $url = '\/?' . $url . '\/?';

        return $url;
    }

    /**
     * @return string
     */
    public function getController() : string
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getFunction() : string
    {
        return $this->function;
    }

    /**
     * @return array
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function getParameterValues() : array
    {
        return array_map(function ($parameter) {
            return $parameter['value'];
        }, $this->parameters);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param array $parameters
     */
    public function setParametersByIndex(array $parameters)
    {
        foreach ($this->parameters as $name => &$parameter) {

            if (!isset($parameters[$parameter['index']])) throw new \Error('No parameter given for index: ' . $parameter['index']);

            $param = $parameters[$parameter['index']];

            // Casting value to appropriate type
            if (is_numeric($param)) {
                if (is_float($param) || is_numeric($param) && ((float) $param != (int) $param)) {
                    $param = (float) $param;
                } else {
                    $param = (int) $param;
                }
            }

            $parameter['value'] = $param;
        }
    }

}
