<?php

class AdminController extends Controller {

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->view->addStyle('components/admin/static/reset.css');
        $this->view->addStyle('components/admin/static/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css');
        $this->view->addStyle('components/admin/static/admin.css');
        $this->view->addScript('components/admin/static/admin.js');
        $this->view->set('adminMenu', $im->get('adminMenu'));
    }

    public function index() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $table = $this->getTable();
        $params = $this->getListParams();
        $pager = new Pager('admin', $params);
        $query = [
            'where' => [],
            'order' => [$params['orderby'] => $params['orderdir']],
            'limit' => [$pager->getPage() * $pager->getStep(), $pager->getStep()]
        ];        
        $pager->setCount($table->count($query));
        $records = $table->find(null, $query);
        $this->view->set('columnViews', $this->getColumnViews());
        $this->view->set('actionButtons', $this->getActionButtons());
        $this->view->set('records', $records);
        $this->view->set('pager', $pager);
        return $this->responseLayout(':admin/layout', ':admin/index');
    }

    public function edit() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        return $this->responseLayout(':admin/layout', ':admin/edit');
    }

    public function delete() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $table = $this->getTable();
        $where = [];
        foreach ($table->getPrimaryKeys() as $pk) {
            $where[] = [$pk, '=', $this->request->get($pk)];
        }
        $record = $table->findOne(null, ['where' => $where]);
        if ($record) {
            $record->delete();
        }
        return $this->redirect('admin', $this->getListParams());
    }

    /**
     * @return Table
     */
    protected function getTable() {
        $im = InstanceManager::getInstance();
        return $im->get('userTable');
    }

    protected function getListParams() {
        return [
            'page' => $this->request->get('page', 0),
            'step' => $this->request->get('step', 10),
            'orderby' => $this->request->get('orderby', 'email'),
            'orderdir' => $this->request->get('orderdir', 'asc')
        ];
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
            new ActionButton('admin/edit', 'edit'),
            new ConfirmActionButton('admin/delete', 'trash')
        ];
    }

}