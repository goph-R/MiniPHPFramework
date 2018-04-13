<?php 

abstract class Validator {

    protected $message = '';
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

    public function getMessage() {
        return str_replace('{label}', $this->label, $this->message);
    }
    
    public function setMessage($message) {
        $this->message = $message;
    }
    
    abstract function doValidate($value);

}