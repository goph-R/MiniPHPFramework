<?php

class PostTableFactory {

    private $defaultLocale;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $translation = $im->get('translation');
        $this->defaultLocale = $translation->getDefaultLocale();
    }

    /**
     * @return Table
     */
    public function createPost() {
        $table = new Table('post');
        $table->addColumn(new IntegerColumn('id'), null, true, true);
        $table->addColumn(new IntegerColumn('user_id'));
        $table->addColumn(new IntegerColumn('created_on'));
        $table->addColumn(new StringColumn('locale', 2), $this->defaultLocale);
        $table->addColumn(new StringColumn('title', 255));
        $table->addColumn(new StringColumn('lead'));
        $table->addColumn(new StringColumn('content'));
        $table->addColumn(new BooleanColumn('active'));
        return $table;
    }

}