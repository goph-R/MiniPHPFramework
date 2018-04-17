<?php

class PageTable extends Table {
    
    public function __construct() {
        parent::__construct();
        $this->name = 'page';
        $im = InstanceManager::getInstance();
        $config = $im->get('config');
        $defaultLocale = $config->get('translation.default', 'en');
        $this->addColumn(new IntegerColumn($this, 'id'), null, true, true);
        $this->addColumn(new StringColumn($this, 'locale', 2), $defaultLocale);
        $this->addColumn(new StringColumn($this, 'name', 255));
        $this->addColumn(new StringColumn($this, 'title', 255));
        $this->addColumn(new StringColumn($this, 'content'));
    }
    
}

