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
    
    protected function getActionButtons() {
        return [
            new ActionButton($this->editRoute, 'pencil-alt'),
            new UserDeleteConfirmActionButton($this->deleteRoute, 'trash')
        ];
    }    

    protected function getFormFactory() {
        return new UserAdminFormFactory();
    }
    
    protected function getListParams() {
        $result = parent::getListParams();
        $result['orderby'] = $this->request->get('orderby', 'email');
        return $result;
    }

    protected function getFilterWhere() {
        $search = $this->filterForm->getValue('search');
        $result = [];
        if ($search) {
            $searchLike = '%'.$search.'%';
            $result[] = [
                ['email', 'like', $searchLike]
            ];
        }
        return $result;
    }
    
    protected function saveForm(Record $record, Form $form) {
        $record->set('email', $form->getValue('email'));
        if ($form->hasInput('password')) {
            $im = InstanceManager::getInstance();
            $userService = $im->get('userService');
            $record->set('password', $userService->hash($form->getValue('password')));
        }
        $record->set('active', $form->getValue('active'));
        return $record;
    }
}