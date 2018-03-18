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

        $step = 4;
        $page = $this->request->get('page', 0);

        $query = [
            'limit' => [$page * $step, $step]
        ];

        $table = $this->im->get('userTable');
        $count = $table->count($query);

        $maxPage = ceil($count / $step);

        $records = $table->find(null, $query);

        $this->view->set('columnViews', [
            new ColumnView($this->im, 'email', 'Email'),
            new ColumnView($this->im, 'firstname', 'Firstname'),
            new ColumnView($this->im, 'lastname', 'Lastname'),
            new BooleanColumnView($this->im, 'active', 'Active')
        ]);

        $this->view->set('actionButtons', [
            new ActionButton(), // edit
            new ActionButton() // delete
        ]);

        $this->view->set('records', $records);

        $this->view->set('step', $step);
        $this->view->set('maxPage', $maxPage);
        $this->view->set('page', $page);

        $this->responseLayout(':admin/layout', ':admin/index');
    }

    public function edit() {

    }

    public function delete() {

    }

}