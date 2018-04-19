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
        $table->addColumn(new IntegerColumn($table, 'id'), null, true, true);
        $table->addColumn(new StringColumn($table, 'locale', 2), $this->defaultLocale);
        $table->addColumn(new StringColumn($table, 'name', 255));
        $table->addColumn(new StringColumn($table, 'title', 255));
        $table->addColumn(new StringColumn($table, 'content'));
        return $table;
    }
    
}

