<?php

class PageAdminController extends AdminController {

    public function __construct() {
        parent::__construct();
        $this->indexRoute = 'admin/page';
        $this->indexTitle = $this->translation->get('pageAdmin', 'pages');
        $this->editRoute = 'admin/page/edit';
        $this->editTitle = $this->translation->get('pageAdmin', 'edit_page');
        $this->view->addPath(':admin/listButtons', 'components/pageAdmin/templates/empty');
    }

    protected function getTable() {
        $im = InstanceManager::getInstance();
        return $im->get('pageTable');
    }

    protected function getColumnViews() {
        return [
            new ColumnView('id', 'ID', 'right'),
            new ColumnView('locale', $this->translation->get('pageAdmin', 'locale')),
            new ColumnView('name', $this->translation->get('pageAdmin', 'name')),
            new ColumnView('title', $this->translation->get('pageAdmin', 'title'), 'left', '100%')
        ];
    }
    
    protected function getActionButtons() {
        return [
            new ActionButton($this->editRoute, 'pencil-alt')
        ];
    }

    protected function getFormFactory() {
        return new PageAdminFormFactory();
    }
    
    protected function getListParams() {
        $result = parent::getListParams();
        $result['orderby'] = $this->request->get('orderby', 'name');
        return $result;
    }

    protected function getFilterWhere() {
        $search = $this->filterForm->getValue('search');
        $result = [];
        if ($search) {
            $searchLike = '%'.$search.'%';
            $result[] = ['or', [
                ['name', 'like', $searchLike],
                ['title', 'like', $searchLike]
            ]];
        }
        return $result;
    }

    protected function saveForm(Record $record, Form $form) {
        $record->set('title', $form->getValue('title'));
        $record->set('content', $form->getValue('content'));
        return $record;
    }
}