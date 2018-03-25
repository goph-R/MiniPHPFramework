<?php

class AdminController extends Controller {

    public function __construct(InstanceManager $im) {
        parent::__construct($im);
        $this->view->addStyle('components/admin/static/reset.css');
        $this->view->addStyle('components/admin/static/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css');
        $this->view->addStyle('components/admin/static/admin.css');
        $this->view->set('adminMenu', $im->get('adminMenu'));
    }

    public function index() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }

        $table = $this->im->get('userTable');
        $pageNumber = $this->request->get('page', 0);
        $pageStep = $this->request->get('step', 4);
        $orderBy = $this->request->get('orderby', 'email');
        $orderDir = $this->request->get('orderdir', 'asc');

        $query = [
            'where' => [],
            'order' => [$orderBy => $orderDir],
            'limit' => [$pageNumber * $pageStep, $pageStep]
        ];        
        $count = $table->count($query);
        $maxPageNumber = ceil($count / $pageStep);
        $records = $table->find(null, $query);

        $this->view->set('columnViews', $this->getColumnViews());
        $this->view->set('actionButtons', $this->getActionButtons());
        $this->view->set('records', $records);

        $this->view->set('pageStep', $pageStep);
        $this->view->set('maxPageNumber', $maxPageNumber);
        $this->view->set('pageNumber', $pageNumber);

        $this->responseLayout(':admin/layout', ':admin/index');
    }

    public function getColumnViews() {
        $im = $this->im;
        return [
            new ColumnView($im, 'email', 'Email'),
            new BooleanColumnView($im, 'active', 'Active')
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