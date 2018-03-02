<?php

class View {

    private $attributes = [];
    private $config;
    private $scripts = [];
    private $styles = [];

    public function __construct($im) {
        $this->config = $im->get('config');
    }

    public function addScript($script) {
        $this->scripts[] = $script;
    }

    public function addStyle($style) {
        $this->styles[] = $style;
    }

    public function getScripts() {
        return $this->scripts;
    }

    public function getStyles() {
        return $this->styles;
    }

    public function set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function get($name, $defaultValue = '') {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return $defaultValue;
    }

    private function getTemplatePath($path) {
        return $this->config->get('application.path').$path.'.'.$this->config->get('view.template.extension');
    }

    public function fetch($path) {
        ob_start();
        extract($this->attributes);
        include($this->getTemplatePath($path));
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    public function escape($string) {
        return htmlspecialchars($string);
    }

}