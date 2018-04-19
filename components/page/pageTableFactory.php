<?php

class PageTableFactory {

    private $defaultLocale;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $config = $im->get('config');
        $this->defaultLocale = $config->get('translation.default', 'en');
    }

    /**
     * @return Table
     */
    public function createPage() {
        $table = new Table('page');
        $table->addColumn(new IntegerColumn('id'), null, true, true);
        $table->addColumn(new StringColumn('locale', 2), $this->defaultLocale);
        $table->addColumn(new StringColumn('name', 255));
        $table->addColumn(new StringColumn('title', 255));
        $table->addColumn(new StringColumn('content'));
        return $table;
    }
    
}

