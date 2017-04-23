<?php

abstract class Controller {
	
	protected $application;
	protected $config;
	protected $response;
	protected $request;
	protected $view;
	protected $router;
	
	public function __construct($application, $config, $router, $request, $response, $user, $db, $view) {
		$this->application = $application;
		$this->config = $config;
		$this->request = $request;
		$this->response = $response;
		$this->router = $router;
		$this->user = $user;
		$this->view = $view;
		$this->db = $db;
		$this->view->set('config', $config);
		$this->view->set('router', $router);
		$this->view->set('request', $request);
		$this->view->set('response', $response);
		$this->view->set('user', $user);
		$this->view->set('view', $view);
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
