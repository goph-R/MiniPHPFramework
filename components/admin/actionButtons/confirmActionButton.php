<?php

class ConfirmActionButton extends ActionButton {

    protected function fetchUrl($params) {
        return "javascript:Admin.confirmRedirect('Are you sure?', '".$this->router->getUrl($this->route, $params)."')";
    }

}