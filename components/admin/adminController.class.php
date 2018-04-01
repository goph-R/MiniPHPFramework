<?php

class AdminController extends Controller {

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->view->addStyle('components/admin/static/reset.css');
        $this->view->addStyle('components/admin/static/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css');
        $this->view->addStyle('components/admin/static/admin.css');
        $this->view->set('adminMenu', $im->get('adminMenu'));
    }

    public function index() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $im = InstanceManager::getInstance();
        $table = $im->get('userTable');
        $params = [
            'orderby' => $this->request->get('orderby', 'email'),
            'orderdir' => $this->request->get('orderdir', 'asc')
        ];

        $pager = new Pager(
            'admin',
            (int)$this->request->get('page', 0),
            (int)$this->request->get('step', 4),
            $params
        );


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

        $this->responseLayout(':admin/layout', ':admin/index');
    }

    public function getColumnViews() {
        return [
            new ColumnView('email', 'Email'),
            new BooleanColumnView('active', 'Active')
        ];
    }

    public function getActionButtons() {
        return [
            new ActionButton(), // edit
            new ActionButton() // delete
        ];
    }

    public function edit() {

    }

    public function delete() {

    }

}