<?php

class PageAdminController extends AdminController {

    public function __construct() {
        parent::__construct();
        $this->indexRoute = 'admin/page';
        $this->indexTitle = $this->translation->get('pageAdmin', 'pages');
        $this->editRoute = 'admin/page/edit';
        $this->editTitle = $this->translation->get('pageAdmin', 'edit_page');
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

    protected function getForm(Record $record) {       
        return new PageAdminForm($record);
    }
    
    protected function getFilterForm() {
        return new UserFilterForm();
    }

    protected function getListParams() {
        $result = parent::getListParams();
        $result['orderby'] = $this->request->get('orderby', 'name');
        return $result;
    }

    protected function getFilterQuery() {
        $search = $this->filterForm->getValue('search');
        $result = [];
        if ($search) {
            $searchLike = '%'.$search.'%';
            $result[] = [
                ['name', 'like', $searchLike],
                ['title', 'like', $searchLike]
            ];
        }
        return $result;
    }
}