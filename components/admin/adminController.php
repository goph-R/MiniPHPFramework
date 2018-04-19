<?php

abstract class AdminController extends Controller {

    protected $indexRoute = 'admin';
    protected $indexTitle = 'Index';
    protected $editTitle = 'Edit';
    protected $editRoute = 'admin/edit';
    protected $addTitle = 'Add';
    protected $addRoute = 'admin/add';
    protected $deleteRoute = 'admin/delete';

    /**
     * @var Form
     */
    protected $filterForm = null;

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
        $this->processFilterForm();
        $listParams = $this->getListParams();
        $query = [];
        $query['where'] = $this->getFilterWhere();
        $query['join'] = $this->getFilterJoins();
        $pager = new Pager('admin', $listParams);
        $pager->setCount($table->count($query));
        $query['order'] = [$listParams['orderby'] => $listParams['orderdir']];
        $query['limit'] = [$pager->getPage() * $pager->getStep(), $pager->getStep()];
        $records = $table->find($this->getListColumns(), $query);
        $this->view->set('columnViews', $this->getColumnViews());
        $this->view->set('actionButtons', $this->getActionButtons());
        $this->view->set('listParams', $listParams);
        $this->view->set('filterForm', $this->filterForm);
        $this->view->set('title', $this->indexTitle);
        $this->view->set('records', $records);
        $this->view->set('pager', $pager);
        $this->view->set('addTitle', $this->addTitle);
        $this->view->set('addRoute', $this->addRoute);
        $this->view->set('indexRoute', $this->indexRoute);
        return $this->responseLayout(':admin/layout', ':admin/index');
    }
    
    private function processFilterForm() {
        $this->filterForm = $this->getFilterForm();
        if ($this->filterForm) {
            $this->filterForm->bind();
            $this->filterForm->validate();
        }        
    }

    public function edit() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $table = $this->getTable();
        $pkValues = $this->getPrimaryKeyValues($table);
        $this->processFilterForm();
        $params = $pkValues + $this->getListParams();
        $record = $table->findOneByPrimaryKeys($pkValues);
        $form = $this->getForm($record);
        if ($form->processInput()) {
            $this->saveForm($record, $form);
            $record->save();
            return $this->redirect($this->indexRoute, $params);
        }        
        $this->view->set('params', $params);
        $this->view->set('form', $form);
        $this->view->set('indexRoute', $this->indexRoute);
        $this->view->set('title', $this->editTitle);
        $this->view->set('action', $this->router->getUrl($this->editRoute, $params));
        return $this->responseLayout(':admin/layout', ':admin/adminForm');
    }

    public function add() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $table = $this->getTable();
        $this->processFilterForm();
        $params = $this->getListParams();
        $record = new Record($table);
        $form = $this->getForm($record);
        if ($form->processInput()) {
            $form->save();
            return $this->redirect($this->indexRoute, $params);
        }
        $this->view->set('params', $params);
        $this->view->set('form', $form);
        $this->view->set('indexRoute', $this->indexRoute);
        $this->view->set('title', $this->addTitle);
        $this->view->set('action', $this->router->getUrl($this->addRoute, $params));
        return $this->responseLayout(':admin/layout', ':admin/adminForm');
    }

    public function delete() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $table = $this->getTable();
        $pkValues = $this->getPrimaryKeyValues($table);
        $record = $table->findOneByPrimaryKeys($pkValues);
        if ($record) {
            $record->delete();
        }
        return $this->redirect($this->indexRoute, $this->getListParams());
    }
    
    protected function getPrimaryKeyValues(Table $table) {
        $result = [];        
        foreach ($table->getPrimaryKeys() as $pk) {
            $result[$pk] = $this->request->get($pk);
        }
        return $result;
    }        
    
    /**
     * @return Form
     */
    protected function getFilterForm() {
        return null;
    }
    
    protected function getListParams() {
        $result = [
            'page' => $this->request->get('page', 0),
            'step' => $this->request->get('step', 10),
            'orderby' => $this->request->get('orderby', 'id'),
            'orderdir' => $this->request->get('orderdir', 'asc')
        ];
        if ($this->filterForm) {
            $result += $this->filterForm->getValues();
        }
        return $result;
    }

    protected function getListColumns() {
        return null;
    }
    
    protected function getFilterWhere() {
        return null;
    }

    protected function getFilterJoins() {
        return null;
    }
    
    protected function getActionButtons() {
        return [
            new ActionButton($this->editRoute, 'pencil-alt'),
            new ConfirmActionButton($this->deleteRoute, 'trash')
        ];
    }
    
    /**
     * @return Table
     */
    abstract protected function getTable();

    abstract protected function getColumnViews();

    /**
     * @param Record
     * @return Form
     */
    abstract protected function getForm(Record $record);

    /**
     * @param Record
     * @param Form
     * @return Record
     */
    abstract protected function saveForm(Record $record, Form $form);

}