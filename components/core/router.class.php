<?php

class Router {

	const PARAMETER_REGEX = '/\:[a-zA-Z0-9_]+/';

	private $map = [];
	private $config;
	private $request;

	public function __construct($im) {
		$this->config = $im->get('config');
		$this->request = $im->get('request');
	}

	public function init() {
		$this->add('', $this->config->get('router.default.controller'), $this->config->get('router.default.method'));		
	}

	public function add($route, $controller, $method) {
		$item = [
			'route' => $route, 
			'controller' => $controller,
			'method' => $method
		];
		$matches = [];			
		preg_match_all(self::PARAMETER_REGEX, $route, $matches, PREG_PATTERN_ORDER);
		if ($matches[0]) {
			$item['parameters'] = $matches[0];
		}
		$this->map[] = $item;
	}

	public function query($route) {
		foreach ($this->map as $item) {
			if (isset($item['parameters'])) {
				$values = $this->fetchParameterValues($route, $item['route']);
				if ($values) {
					$this->setRequestValues($values, $item['parameters']);
					return $item;
				}
			} else if ($route == $item['route']) {
				return $item;
			}			
		}
		return null;
	}

	private function fetchParameterValues($route, $itemRoute) {
		$regex = preg_replace(self::PARAMETER_REGEX, '([^/]+)', $itemRoute);
		$regex = '/'.str_replace('/', '\\/', $regex).'/';
		$matches = [];
		preg_match_all($regex, $route, $matches, PREG_SET_ORDER);
		return isset($matches[0]) ? $matches[0] : null;
	}

	private function setRequestValues($values, $parameters) {
		for ($i = 0; $i < count($parameters); $i++) {
			$name = substr($parameters[$i], 1);
			$value = $values[$i+1];
			$this->request->set($name, $value);
		}
	}

	public function queryCurrent() {
		$param = $this->config->get('router.parameter', '');
		$value = $this->request->get($param);
		return $this->query($value);
	}

	public function getUrl($path) {
		if ($this->config->get('router.rewrite')) {
			return $this->config->get('router.base').$path;			
		}
		return $this->config->get('router.base').'?'.$this->config->get('router.parameter', '').'='.$path;
	}
}