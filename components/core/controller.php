<?php

abstract class Controller {

    const InstanceNames = ['config', 'request', 'response', 'router', 'db', 'view', 'user', 'app', 'translation'];
    const OneYearInSeconds = 60*60*24*365;
    /**
     * @var InstanceManager
     */
    protected $im;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var WebApplication
     */
    protected $app;

    /**
     * @var Translation
     */
    protected $translation;

    public function __construct() {
        $im = InstanceManager::getInstance();
        foreach (self::InstanceNames as $name) {
            $this->$name = $im->get($name);
        }
        foreach (self::InstanceNames as $name) {
            $this->view->set($name, $im->get($name));
        }
    }

    public function respondView($template) {
        $this->response->setContent($this->view->fetch($template));
    }

    public function respondLayout($layout, $template) {
        $this->view->set('content', $this->view->fetch($template));
        $this->response->setContent($this->view->fetch($layout));
    }

    public function respondJson($data) {
        $this->response->setContent(json_encode($data));
    }
        
    public function respond404() {
        $this->app->sendNotFound();
    }
    
    public function respond500() {
        $this->app->sendInternalServerError();
    }
    
    public function respondCacheableFile($content, $size, $mime='text/plain', $expDelta=self::OneYearInSeconds) {
        $expTime = gmdate('D, d M Y H:i:s', time() + $expDelta).' GMT';
        $this->response->setHeaders([            
            'Content-Type'   => $mime,
            'Content-Length' => $size,
            'Expires'        => $expTime,
            'Pragma'         => 'cache',
            'Cache-Control'  => "max-age=$expDelta"
        ]);
        $this->response->setContent($content);        
    }

    public function redirect($route = '', $params=[]) {
        $this->redirectToUrl($this->router->getUrl($route, $params, false));
    }

    public function redirectToUrl($url) {
        $this->response->setHeader('Location', $url);
        $this->response->setContent('');
        /*
        $this->response->send();
        $this->app->finish();
        exit();
        */
    }

}
