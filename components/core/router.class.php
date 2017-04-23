<?php

class Router {

	private $map = [];
	private $config;
	private $request;

	public function __construct($config, $request) {
		$this->config = $config;
		$this->request = $request;
	}

	public function add($map, $controller, $method) {
		$this->map[] = [
			'map' => $map, 
			'controller' => $controller,
			'method' => $method
		];
	}

	public function query($route) {
		if (!$route) {
			return [
				'controller' => $this->config->get('router.default.controller'),
				'method' => $this->config->get('router.default.method')
			];
		}
		foreach ($this->map as $item) {
			if (preg_match($item['map'], $route)) {
				return $item;
			}
		}
		return null;
	}

	public function queryCurrent() {
		return $this->query($this->request->get($this->config->get('router.parameter', '')));
	}

	public function getUrl($path) {
		if ($this->config->get('router.rewrite')) {
			return $this->config->get('router.base').$path;			
		}
		return $this->config->get('router.base').'?'.$this->config->get('router.parameter', '').'='.$path;
	}
}