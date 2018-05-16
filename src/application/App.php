<?php

namespace App;


class App
{
    private $container;
    public function __construct($container)
    {
        $this->container = $container;
    }
    public function __get($property)
    {
        if (isset($this->container->{$property})){
            return $this->container->{$property};
        }
        return null;
    }
    public function redirect($routeName = '')
    {
        return $this->response->withRedirect($this->router->pathFor($routeName));
    }
}