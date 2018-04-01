<?php

class Pager {

    private $page;
    private $step;
    private $count;
    private $maxPage;
    private $params;

    public function __construct($route, $params=[]) {
        $im = InstanceManager::getInstance();
        $this->page = (int)$params['page'];
        $this->step = (int)$params['step'];
        $this->params = $params;
        $this->route = $route;
        $this->router = $im->get('router');
    }

    public function setCount($count) {
        $this->count = $count;
        $this->maxPage = ceil($count / $this->step);
    }

    public function getPage() {
        return $this->page;
    }

    public function getMaxPage() {
        return $this->maxPage;
    }

    public function getStep() {
        return $this->step;
    }

    public function getUrl($page) {
        $params = $this->params;
        $params['page'] = $page;
        return $this->router->getUrl($this->route, $params);
    }

}