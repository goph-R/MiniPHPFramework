<?php

class PasswordValidator extends Validator {

    const DEFAULT_OPTIONS = [
        'minLength' => 8,
        'minLowerCase' => 0,
        'minUpperCase' => 0,
        'minNumber' => 0,
        'minSpecialChar' => 0,
        'specialChars' => '!@#$%^&+-*/=_'
    ];
    
    const MESSAGE = [
        'minLowerCase' => 'password_use_more_lowercase',
        'minUpperCase' => 'password_use_more_uppercase',
        'minNumber' => 'password_use_more_numbers',
        'minSpecialChar' => 'password_use_more_specialchars'
    ];
    
    private $regex = [
        'minLowerCase' => '/[^a-z]+/',
        'minUpperCase' => '/[^A-Z]+/',
        'minNumber' => '/[^0-9]+/',
        'minSpecialChar' => ''
    ];
    
    public function __construct($options = self::DEFAULT_OPTIONS) {
        parent::__construct();
        $this->error = $this->translation->get('core', 'password_not_valid');        
        $this->options = self::DEFAULT_OPTIONS + $options;
        $regex = '';
        for ($i = 0; $i < strlen($this->options['specialChars']); $i++) {
            $regex .= '\\'.$this->options['specialChars'][$i];
        }
        $this->regex['minSpecialChar'] = '/[^'.$regex.']+/';
    }

    public function doValidate($value) {
        if (function_exists('iconv')) {
            $value = iconv('UTF-8','ASCII//TRANSLIT', $value);
        }
        $min = $this->options['minLength'];
        if ($this->options['minLength'] && mb_strlen($value) < $min) {
            $message = $this->translation->get('core', 'password_too_short');
            $this->error = str_replace('{min}', $min, $message);
            return false;
        }        
        foreach ($this->regex as $name => $regex) {
            $min = $this->options[$name];      
            $count = mb_strlen(preg_replace($regex, '', $value));
            if ($this->options[$name] && $count < $min) {
                $message = $this->translation->get('core', self::MESSAGE[$name]);
                $this->error = str_replace(['{min}', '{chars}'], [$min, $this->options['specialChars']], $message);
                return false;
            }
        }
        return true;
    }

}