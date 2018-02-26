<?php

abstract class Controller {

    const InstanceNames = ['config', 'request', 'response', 'router', 'db', 'view', 'user', 'app'];

    protected $im;

	public function __construct($im) {
	    $this->im = $im;
		foreach (self::InstanceNames as $name) {
			$this->$name = $im->get($name);
		}
        foreach (self::InstanceNames as $name) {
            $this->view->set($name, $im->get($name));
        }
	}

	public function responseView($template) {
		$this->response->setContent($this->view->fetch($template));
	}

	public function responseLayout($layout, $template) {
		$this->view->set('content', $this->view->fetch($template));
		$this->response->setContent($this->view->fetch($layout));
	}

	public function responseJson($data) {
		$this->response->setContent(json_encode($data));
	}

	public function redirect($route = '') {
	    $this->redirectToUrl($this->router->getUrl($route));
    }

	public function redirectToUrl($url) {
		$this->response->setHeader('Location', $url);
		$this->response->setContent('');
	}
}
