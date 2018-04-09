<?php

class Pager {

    const MAX_STEP = 100;

    private $page;
    private $step;
    private $count;
    private $maxPage;
    private $params;

    public function __construct($route, $params=[]) {
        $im = InstanceManager::getInstance();
        $this->page = (int)$params['page'];
        if ($this->page < 0) {
            $this->page = 0;
        }
        $this->step = (int)$params['step'];
        if ($this->step < 1) {
            $this->step = 1;
        } else if ($this->step > self::MAX_STEP) {
            $this->step = self::MAX_STEP;
        }
        $this->params = $params;
        $this->route = $route;
        $this->router = $im->get('router');
    }

    public function setCount($count) {
        $this->count = $count;
        if ($count == 0) {
            $this->page = 0;
        } else {
            $this->maxPage = ceil($count / $this->step);
            if ($this->page >= $this->maxPage) {
                $this->page = $this->maxPage - 1;
            }
        }
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

    public function getUrl($page, $escapeAmp=true) {
        $params = $this->params;
        $params['page'] = $page;
        return $this->router->getUrl($this->route, $params, $escapeAmp);
    }

    public function hasNext() {
        return $this->getPage() != $this->getMaxPage() - 1;
    }

    public function hasPrev() {
        return $this->getPage() != 0;
    }

    public function getLastUrl() {
        return $this->getUrl($this->getMaxPage() - 1);
    }

    public function getNextUrl() {
        return $this->getUrl($this->getPage() + 1);
    }
    public function getPrevUrl() {
        return $this->getUrl($this->getPage() - 1);
    }

    public function getFirstUrl() {
        return $this->getUrl(0);
    }

}