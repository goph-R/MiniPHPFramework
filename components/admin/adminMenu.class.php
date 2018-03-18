<?php

class AdminMenu {

    private $items = [];

    public function addItem($title, $route, $icon) {
        $this->items[] = new AdminMenuItem($title, $route, $icon);
    }

    public function getItems() {
        return $this->items;
    }

}