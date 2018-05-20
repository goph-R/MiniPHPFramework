<?php

class CkEditorInput extends Input {

    private $options;
    private $stylePath;
    
    public function __construct($name, $defaultValue='', $options=[]) {
        parent::__construct($name, $defaultValue);
        $im = InstanceManager::getInstance();
        $config = $im->get('config');
        $this->stylePath = $config->get('ckeditor.style');
        $this->options = $options;
        $this->view->addScript('vendor/ckeditor/ckeditor.js');
    }

    public function setOption($name, $value) {
        $this->options[$name] = $value;
    }

    public function getOption($name) {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }
    
    public function fetch() {
        $result = '<textarea ';        
        $result .= ' id="'.$this->getId().'"';
        $result .= ' name="'.$this->getName().'"';
        $result .= $this->getClassHtml();        
        $result .= '>'.$this->getValue().'</textarea>';
        $options = [
            'language' => $this->request->get('locale'),
            'width' => 980,
            'height' => 400,
            'resize_enabled' => true
        ];
        if ($this->stylePath) {
            $options['contentsCss'] = $this->stylePath;
        }
        $optionsStr = json_encode(array_merge($options, $this->options));
        $this->view->addScriptContent("CKEDITOR.replace('".htmlspecialchars($this->getId())."', ".$optionsStr.");");
        return $result;
    }
    
}
