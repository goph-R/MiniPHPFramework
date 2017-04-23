<?php

class WebApplication {

	protected $config;
	protected $router;
	protected $reponse;
	protected $response;
	protected $user;
	protected $view;
	protected $db;

	public function __construct($config, $router, $request, $response, $user, $db, $view) {
		$this->config = $config;
		$this->request = $request;
		$this->response = $response;
		$this->router = $router;
		$this->view = $view;
		$this->user = $user;
		$this->db = $db;
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
		$controller = new $controllerClass(
			$this,
			$this->config,
			$this->router,
			$this->request,
			$this->response,			
			$this->user,
			$this->db,
			$this->view
		);
		$method = $result['method'];
		if (!method_exists($controller, $method)) {
			die('404 (method)');
		}
		$controller->$method();		
		$this->response->send();		
	}
}
