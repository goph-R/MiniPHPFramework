<?php

class ColumnView {

    protected $columnName;
    protected $label;
    protected $width;
    protected $align;
    protected $route;
    protected $alias;

    /**
     * @var Router
     */
    protected $router;

    public function __construct($columnName, $label, $align='left', $width=null) {
        $this->columnName = $columnName;
        $this->label = $label;
        $this->width = $width;
        $this->align = $align;
        $im = InstanceManager::getInstance();
        $this->router = $im->get('router');
    }

    public function setAlias($alias) {
        $this->alias = $alias;
    }

    public function getAlign() {
        return $this->align;
    }

    public function fetch(Record $record) {
        $columnName = $this->alias ? $this->alias : $this->columnName;
        $value = htmlspecialchars($record->get($columnName));
        $value = str_replace(['-', ' '], ['&#8209;', '&nbsp;'], $value);
        return $value;
    }

    public function fetchHeader($listParams, $indexRoute) {
        $orderBy = @$listParams['orderby'];
        $listParams = $this->adjustListParams($listParams);
        $html = '<th';
        if ($this->width) {
            $html .= ' style="width: '.$this->width.'"';
        }
        $html .= '><a href="'.$this->router->getUrl($indexRoute, $listParams).'">';
        $html .= htmlspecialchars($this->label);
        if ($orderBy == $this->columnName) {
            if ($listParams['orderdir'] == 'desc') {
                $html .= '&nbsp;<i class="fa fa-angle-up"></i>';
            } else {
                $html .= '&nbsp;<i class="fa fa-angle-down"></i>';
            }
        }
        $html .= '</a></th>';
        return $html;
    }

    protected function adjustListParams($listParams) {
        $orderBy = @$listParams['orderby'];
        $orderDir = @$listParams['orderdir'];
        if ($orderBy == $this->columnName) {
            $orderDir = $orderDir == 'asc' ? 'desc' : 'asc';
        } else {
            $orderDir = 'asc';
        }
        $listParams['orderby'] = $this->columnName;
        $listParams['orderdir'] = $orderDir;
        return $listParams;
    }

}