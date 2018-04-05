<?php

class ActionButton {

    /**
     * @var Router
     */
    protected $router;

    protected $icon;
    protected $route;

    public function __construct($route, $icon) {
        $im = InstanceManager::getInstance();
        $this->router = $im->get('router');
        $this->route = $route;
        $this->icon = $icon;
    }

    public function fetch(Record $record, $params=[]) {
        $table = $record->getTable();
        foreach ($table->getPrimaryKeys() as $pk) {
            $params[$pk] = $record->get($pk);
        }
        $html = '<a class="action-button" href="';
        $html .= $this->fetchUrl($params);
        $html .= '"><i class="fa fa-'.$this->icon.'"></i></a>';
        return $html;
    }

    protected function fetchUrl($params) {
        return $this->router->getUrl($this->route, $params);
    }

}