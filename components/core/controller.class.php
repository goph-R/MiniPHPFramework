<?php

abstract class Controller {
	
	public function __construct($im) {
		foreach ($im->getAll() as $name => $instance) {
			$this->$name = $instance;
		}
		foreach ($im->getAll() as $name => $instance) {
			$this->view->set($name, $instance);
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

	public function redirect($url) {
		$this->response->setHeader('Location', $url);
		$this->response->setContent('');
	}
}
