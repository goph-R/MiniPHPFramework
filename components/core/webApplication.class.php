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

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct() {
        $this->im = InstanceManager::getInstance();
        $this->router = $this->im->get('router');
        $this->response = $this->im->get('response');
        $this->logger = $this->im->get('logger');
    }

    public function run() {
        try {
            $this->tryToRun();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->sendInternalServerError();
        }
    }

    private function tryToRun() {
        $this->im->init();
        $route = $this->router->queryCurrent();
        $found = false;
        if ($route) {
            $controllerClass = $route['controller'];
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                $method = $route['method'];
                if (method_exists($controller, $method)) {
                    $found = true;
                    $controller->$method();
                }
            }
        }
        if (!$found) {
            $this->sendNotFound();
        } else {   
            $this->response->send();
        }
        $this->im->finish();        
    }

    private function sendNotFound() {
        header("HTTP/1.0 404 Not Found");
        include __DIR__.'/static/404.html';
    }

    private function sendInternalServerError() {
        header("HTTP/1.0 500 Internal Server Error");
        include __DIR__.'/static/500.html';
    }
}
