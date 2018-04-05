<?php

class ConfirmActionButton extends ActionButton {

    public function __construct($route, $icon) {
        parent::__construct($route, $icon);
    }

    protected function fetchUrl($params) {
        return "javascript:Admin.confirmRedirect('Are you sure?', '".$this->router->getUrl($this->route, $params)."')";
    }

}