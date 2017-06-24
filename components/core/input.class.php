<?php

abstract class Input {

	protected $view;
	protected $name;
	protected $error;
	protected $defaultValue;
	protected $scripts = [];
	protected $styles = [];
	protected $classes = [];
	protected $value;

	public function __construct($view, $name, $defaultValue = '') {
		$this->view = $view;
		$this->name = $name;
		$this->defaultValue = $defaultValue;
		$this->create();
	}

	public function create() {}

	public function setError($error) {
		$this->error = $error;
	}

	public function getClasses() {
		$classes = $this->classes;
		if ($this->hasError()) {
			$classes[] = 'error';
		}
		return join($classes, ' ');
	}

	public function hasError() {
		return (boolean)$this->error;
	}

	public function getError() {
		return $this->error;
	}

	public function getScripts() {
		return $this->scripts;
	}

	public function getStyles() {
		return $this->styles;
	}

	public function getName() {
		return $this->name;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getDefaultValue() {
		return $this->defaultValue;
	}

}