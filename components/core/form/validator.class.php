<?php 

abstract class Validator {

    protected $error = '';
    protected $replacedError = '';

    /**
     * @var Translation
     */
    protected $translation;

    public function __construct(InstanceManager $im) {
        $this->translation = $im->get('translation');
    }

    public function validate($label, $value) {
        $this->replacedError = str_replace('{label}', $label, $this->error);
        return $this->doValidate($value);
    }

    public function getError() {
        return $this->replacedError;
    }

    public function setError($error) {
        $this->error = $error;
    }

    abstract function doValidate($value);

}