<?php

class AdminMenuItem {

    private $icon;
    private $title;
    private $route;

    public function __construct($title, $route, $icon) {
        $this->title = $title;
        $this->route = $route;
        $this->icon = $icon;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getRoute() {
        return $this->route;
    }


}