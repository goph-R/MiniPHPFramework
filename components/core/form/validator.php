<?php 

abstract class Validator {

    protected $error = '';
    protected $label = '';

    /**
     * @var Translation
     */
    protected $translation;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $this->translation = $im->get('translation');
    }

    public function validate($label, $value) {
        $this->label = $label;
        return $this->doValidate($value);
    }

    public function getError() {
        return str_replace('{label}', $this->label, $this->error);
    }
    
    abstract function doValidate($value);

}