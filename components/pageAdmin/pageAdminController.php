<?php

class PageAdminController extends AdminController {

    /**
     * @var PageTableFactory
     */
    private $pageTableFactory;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->pageTableFactory = $im->get('pageTableFactory');
        $this->indexRoute = 'admin/pages';
        $this->indexTitle = $this->translation->get('pageAdmin', 'pages');
        $this->editRoute = 'admin/page/edit';
        $this->editTitle = $this->translation->get('pageAdmin', 'edit_page');
        $this->view->addPath(':admin/listButtons', 'components/pageAdmin/templates/empty');
    }

    protected function createTable() {
        return $this->pageTableFactory->createPage();
    }

    protected function createColumnViews() {
        return [
            new ColumnView('id', 'ID', 'right'),
            new ColumnView('locale', $this->translation->get('pageAdmin', 'locale')),
            new ColumnView('name', $this->translation->get('pageAdmin', 'name')),
            new ColumnView('title', $this->translation->get('pageAdmin', 'title'), 'left', '100%')
        ];
    }
    
    protected function createActionButtons() {
        return [
            new ActionButton($this->editRoute, 'pencil-alt')
        ];
    }

    protected function createFormFactory() {
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
        $fields = ['title', 'content'];
        $record->setAll($fields, $form->getValues());
        return $record;
    }
}