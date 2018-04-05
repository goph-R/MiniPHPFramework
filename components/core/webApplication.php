<?php

class WebApplication {

    /**
     * @var Config
     */
    protected $config;

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
        $this->config = $this->im->get('config');
    }

    public function run() {
        try {
            $this->runCore();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->sendInternalServerError();
        }
    }

    private function runCore() {
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
        include $this->config->get('application.error_path.404');
    }

    private function sendInternalServerError() {
        header("HTTP/1.0 500 Internal Server Error");
        include $this->config->get('application.error_path.500');
    }
}
