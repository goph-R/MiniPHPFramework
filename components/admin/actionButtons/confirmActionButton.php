<?php

class ConfirmActionButton extends ActionButton {

    protected function fetchUrl($params) {
        return "javascript:Confirm.redirect('delete', '".$this->router->getUrl($this->route, $params)."')";
    }

}