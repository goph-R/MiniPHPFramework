<?php

abstract class AdminController extends Controller {

    protected $viewPath = ':admin';

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
        $listParams = $this->getListParams();
        $query = [];
        $pager = new Pager('admin', $listParams);
        $pager->setCount($table->count($query));
        $query['order'] = [$listParams['orderby'] => $listParams['orderdir']];
        $query['limit'] = [$pager->getPage() * $pager->getStep(), $pager->getStep()];
        $records = $table->find(null, $query);
        $this->view->set('columnViews', $this->getColumnViews());
        $this->view->set('actionButtons', $this->getActionButtons());
        $this->view->set('listParams', $listParams);
        $this->view->set('records', $records);
        $this->view->set('pager', $pager);
        return $this->responseLayout(':admin/layout', $this->viewPath.'/index');
    }

    public function edit() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        return $this->responseLayout(':admin/layout', $this->viewPath.'/edit');
    }

    public function add() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        return $this->responseLayout(':admin/layout', $this->viewPath.'/add');
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

    protected function getListParams() {
        return [
            'page' => $this->request->get('page', 0),
            'step' => $this->request->get('step', 10),
            'orderby' => $this->request->get('orderby', 'email'),
            'orderdir' => $this->request->get('orderdir', 'asc')
        ];
    }

    /**
     * @return Table
     */
    abstract protected function getTable();
    abstract protected function getColumnViews();
    abstract protected function getActionButtons();

}