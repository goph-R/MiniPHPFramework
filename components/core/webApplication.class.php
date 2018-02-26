<?php

class WebApplication {

    protected $im;
    protected $router;

    public function __construct($im) {
        $this->im = $im;
        $this->router = $im->get('router');
        $this->response = $im->get('response');
    }

    public function run() {
        $result = $this->router->queryCurrent();
        if (!$result) {
            die('404 (router)');
        }
        $controllerClass = $result['controller'];
        if (!class_exists($controllerClass)) {
            die('404 (class)');
        }
        $controller = new $controllerClass($this->im);
        $method = $result['method'];
        if (!method_exists($controller, $method)) {
            die('404 (method)');
        }
        $controller->$method();
        $this->response->send();
    }
}
