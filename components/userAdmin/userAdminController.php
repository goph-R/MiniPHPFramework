<?php

class UserAdminController extends AdminController {

    /**
     * @var UserTableFactory
     */
    private $userTableFactory;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->userTableFactory = $im->get('userTableFactory');
        $this->userService = $im->get('userService');
        $this->indexTitle = $this->translation->get('userAdmin', 'users');
        $this->addRoute = 'admin/user/add';
        $this->addTitle = $this->translation->get('userAdmin', 'add_user');
        $this->editRoute = 'admin/user/edit';
        $this->editTitle = $this->translation->get('userAdmin', 'edit_user');
        $this->deleteRoute = 'admin/user/delete';
    }

    protected function createTable() {
        return $this->userTableFactory->createUser();
    }

    protected function createColumnViews() {
        return [
            new ColumnView('id', 'ID', 'right'),
            new ColumnView('email', 'Email', 'left', '100%'),
            new BooleanColumnView('active', $this->translation->get('userAdmin', 'active'), 'center')
        ];
    }
    
    protected function createActionButtons() {
        return [
            new ActionButton($this->editRoute, 'pencil-alt'),
            new UserDeleteConfirmActionButton($this->deleteRoute, 'trash')
        ];
    }    

    protected function createFormFactory() {
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
        if ($form->hasInput('password')) {
            $record->set('password', $this->userService->hash($form->getValue('password')));
        }
        $fields = ['email', 'active'];
        $record->setAll($fields, $form->getValues());
        return $record;
    }
}