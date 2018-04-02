<?php

class UserAdminController extends AdminController {

    protected function getTable() {
        $im = InstanceManager::getInstance();
        return $im->get('userTable');
    }

    protected function getColumnViews() {
        return [
            new ColumnView('id', 'ID', 'right'),
            new ColumnView('email', 'Email', 'left', '100%'),
            new BooleanColumnView('active', 'Active', 'center')
        ];
    }

    protected function getActionButtons() {
        return [
            new ActionButton('admin/user/edit', 'edit'),
            new ConfirmActionButton('admin/user/delete', 'trash')
        ];
    }

}