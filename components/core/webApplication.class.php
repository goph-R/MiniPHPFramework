<?php

class WebApplication {

    /**
     * @var InstanceManager
     */
    protected $im;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Response
     */
    protected $response;

    public function __construct() {
        $this->im = InstanceManager::getInstance();
        $this->router = $this->im->get('router');
        $this->response = $this->im->get('response');
    }

    public function run() {
        $this->im->init();
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
        $this->im->finish();
    }
}
