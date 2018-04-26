<?php

class CkEditorInput extends Input {
    
    private $options;
    
    public function __construct($name, $defaultValue='', $options=[]) {
        parent::__construct($name, $defaultValue);
        $this->options = $options;
        $this->view->addScript('vendor/ckeditor/ckeditor.js');
        
    }
    
    public function fetch() {
        $result = '<textarea ';        
        $result .= ' id="'.$this->getId().'"';
        $result .= ' name="'.$this->getName().'"';
        $result .= $this->getClassHtml();        
        $result .= '>'.$this->getValue().'</textarea>';
        $options = [
            'language' => $this->request->get('locale')
        ];
        $optionsStr = json_encode(array_merge($options, $this->options));
        $this->view->addScriptContent("CKEDITOR.replace('".htmlspecialchars($this->getId())."', ".$optionsStr.");");
        return $result;
    }
    
}
