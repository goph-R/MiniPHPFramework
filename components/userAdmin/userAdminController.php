<?php

class UserAdminController extends AdminController {

    public function __construct() {
        parent::__construct();
        $this->indexTitle = $this->translation->get('userAdmin', 'users');
        $this->addRoute = 'admin/user/add';
        $this->addTitle = $this->translation->get('userAdmin', 'add_user');
        $this->editRoute = 'admin/user/edit';
        $this->editTitle = $this->translation->get('userAdmin', 'edit_user');
        $this->deleteRoute = 'admin/user/delete';
    }

    protected function getTable() {
        $im = InstanceManager::getInstance();
        return $im->get('userTable');
    }

    protected function getColumnViews() {
        return [
            new ColumnView('id', 'ID', 'right'),
            new ColumnView('email', 'Email', 'left', '100%'),
            new BooleanColumnView('active', $this->translation->get('userAdmin', 'active'), 'center')
        ];
    }

    protected function getForm(Record $record) {       
        return new UserAdminForm($record);
    }
    
    protected function getFilterForm() {
        return new UserFilterForm();
    }

    protected function getFilterQuery() {
        $search = $this->filterForm->getValue('search');
        $result = [];
        if ($search) {
            $searchLike = '%'.$search.'%';
            $result[] = ['or', [
                ['email', 'like', $searchLike],
                ['firstname', 'like', $searchLike],
                ['lastname', 'like', $searchLike]
            ]];
        }
        return $result;
    }
}