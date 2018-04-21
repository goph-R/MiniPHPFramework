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
     * @var AdminFormFactory
     */
    protected $formFactory;

    /**
     * @var Form
     */
    protected $filterForm;

    /**
     * @var ConfirmScript
     */
    protected $confirmScript;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->confirmScript = $im->get('confirmScript');
        $this->formFactory = $this->createFormFactory();
        $this->view->addStyle('components/admin/static/reset.css');
        $this->view->addStyle('components/admin/static/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css');
        $this->view->addStyle('components/admin/static/admin.css');
        $this->view->set('adminMenu', $im->get('adminMenu'));
    }

    public function index() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $table = $this->createTable();
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
        $this->confirmScript->add();
        $this->view->set([
            'columnViews'   => $this->createColumnViews(),
            'actionButtons' => $this->createActionButtons(),
            'listParams'    => $listParams,
            'filterForm'    => $this->filterForm,
            'title'         => $this->indexTitle,
            'records'       => $records,
            'pager'         => $pager,
            'addTitle'      => $this->addTitle,
            'addRoute'      => $this->addRoute,
            'indexRoute'    => $this->indexRoute
        ]);

        return $this->respondLayout(':admin/layout', ':admin/index');
    }
    
    private function processFilterForm() {
        $this->filterForm = $this->formFactory->createFilterForm();
        if ($this->filterForm) {
            $this->filterForm->bind();
            $this->filterForm->validate();
        }        
    }

    public function edit() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $this->processFilterForm();
        $table = $this->createTable();
        $pkValues = $this->getPrimaryKeyValues($table);
        $params = $pkValues + $this->getListParams();
        $record = $table->findOneByPrimaryKeys($pkValues);
        $form = $this->formFactory->createForm($record);
        if ($form->processInput()) {
            $this->saveForm($record, $form);
            $record->save();
            return $this->redirect($this->indexRoute, $params);
        }
        $this->view->set([
            'params'     => $params,
            'form'       => $form,
            'indexRoute' => $this->indexRoute,
            'title'      => $this->editTitle,
            'action'     => $this->router->getUrl($this->editRoute, $params)
        ]);
        return $this->respondLayout(':admin/layout', ':admin/adminForm');
    }

    public function add() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $table = $this->createTable();
        $this->processFilterForm();
        $params = $this->getListParams();
        $record = new Record($table);
        $form = $this->formFactory->createForm($record);
        if ($form->processInput()) {
            $this->saveForm($record, $form);
            $record->save();
            return $this->redirect($this->indexRoute, $params);
        }
        $this->view->set([
            'params'     => $params,
            'form'       => $form,
            'indexRoute' => $this->indexRoute,
            'title'      => $this->addTitle,
            'action'     => $this->router->getUrl($this->addRoute, $params)
        ]);
        return $this->respondLayout(':admin/layout', ':admin/adminForm');
    }

    public function delete() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $table = $this->createTable();
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
    
    protected function getListParams() {
        $result = [
            'page'     => $this->request->get('page', 0),
            'step'     => $this->request->get('step', 10),
            'orderby'  => $this->request->get('orderby', 'id'),
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

    /**
     * @return ActionButton[]
     */
    protected function createActionButtons() {
        return [
            new ActionButton($this->editRoute, 'pencil-alt'),
            new ConfirmActionButton($this->deleteRoute, 'trash')
        ];
    }
    
    /**
     * @return Table
     */
    abstract protected function createTable();

    /**
     * @return ColumnView[]
     */
    abstract protected function createColumnViews();

    /**
     * @return AdminFormFactory
     */
    abstract protected function createFormFactory();

    /**
     * @param Record
     * @param Form
     * @return Record
     */
    abstract protected function saveForm(Record $record, Form $form);

}