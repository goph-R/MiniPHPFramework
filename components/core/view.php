<?php

class View {

    /**
     * @var Config
     */
    private $config;

    private $attributes = [];
    private $scripts = [];
    private $scriptContents = [];
    private $styles = [];
    private $paths = [];

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->config = $im->get('config');
    }

    public function addScript($script) {
        $key = strtolower($script);
        $this->scripts[$key] = $script;
    }
    
    public function addScriptContent($scriptContent) {
        $this->scriptContents[] = $scriptContent;
    }

    public function addStyle($style, $media='all') {
        $key = strtolower($style.';'.$media);
        $this->styles[$key] = [
            'source' => $style,
            'media' => $media
        ];
    }

    public function getScripts() {
        return $this->scripts;
    }

    public function getStyles() {
        return $this->styles;
    }

    public function set($name, $value=null) {
        if (is_array($name)) {
            $this->attributes += $name;
        } else {
            $this->attributes[$name] = $value;
        }
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

    public function addPath($name, $path) {
        $this->paths[$name] = $path;
    }

    private function findPath($path) {
        if ($path[0] != ':') {
            return $path;
        }
        if (isset($this->paths[$path])) {
            return $this->paths[$path];
        }
        $perPos = mb_strpos($path, '/');
        $pathName = mb_substr($path, 1, $perPos - 1);
        if (isset($this->paths[$pathName])) {
            $path = $this->paths[$pathName].'/'.mb_substr($path, $perPos + 1, mb_strlen($path));
        }

        return $path;
    }

    public function hasScriptContent() {
        return $this->scriptContents ? true : false;
    }

    public function getScriptContent() {
        if ($this->scriptContents) {
            return join("\r\n", $this->scriptContents);
        }
        return '';
    }

    public function fetch($path, $vars=[]) {
        $path = $this->findPath($path);
        ob_start();
        extract($this->attributes);
        extract($vars);
        include($this->getTemplatePath($path));
        $result = ob_get_clean();
        //$result = str_replace('    ', '', $result);
        return $result;
    }

    public function escape($string) {
        return htmlspecialchars($string);
    }

}