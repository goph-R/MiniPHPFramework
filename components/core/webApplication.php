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

    public static function dispatch($configPath, $environment, $components) {
        $im = InstanceManager::getInstance();
        $config = new Config($configPath, $environment);
        $im->add('config', $config);
        $im->add('logger', new Logger($config));
        $im->add('db', new DB($config->get('db.name')));
        $im->add('request', new Request());
        $im->add('response', new Response());
        $im->add('router', new Router());
        $im->add('view', new View());
        $im->add('user', new User());
        $im->add('mailer', new Mailer());
        $im->add('translation', new Translation());
        $app = new WebApplication();
        $im->add('app', $app);
        $im->initComponents($components);
        $app->run();
    }

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
            $message = $e->getMessage()."\r\n".$e->getTraceAsString();
            $this->logger->error($message);
            $this->sendInternalServerError();
        }
    }
    
    private function runCore() {
        $this->im->get('translation')->add('core', 'components/core/translations');
        $this->im->get('view')->addPath('core', 'components/core/templates');
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
        $this->finish();
    }
    
    public function finish() {
        $this->im->finish();
    }

    public function sendNotFound() {
        header("HTTP/1.0 404 Not Found");
        include $this->config->get('application.error_path.404');
    }

    public function sendInternalServerError() {
        header("HTTP/1.0 500 Internal Server Error");
        include $this->config->get('application.error_path.500');
    }
}
