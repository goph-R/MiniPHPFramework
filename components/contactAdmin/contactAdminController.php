<?php

class ContactAdminController extends AdminController {

    /**
     * @var ContactTableFactory
     */
    private $contactTableFactory;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->contactTableFactory = $im->get('contactTableFactory');
        $this->indexRoute = 'admin/contacts';
        $this->indexTitle = $this->translation->get('contactAdmin', 'contacts');
        $this->view->addPath(':admin/listButtons', 'components/contactAdmin/templates/empty');
    }

    protected function createTable() {
        return $this->contactTableFactory->createContact();
    }

    protected function createColumnViews() {
        return [
            new ColumnView('email', 'Email', 'left', '100%'),
            new ColumnView('name', $this->translation->get('contact', 'name')),
            new DateColumnView('created_on', $this->translation->get('contactAdmin', 'created_on'))
        ];
    }

    protected function createActionButtons() {
        return [
            new ActionButton('admin/contact/view', 'eye')
        ];
    }

    protected function createFormFactory() {
        return new AdminFormFactory();
    }

    protected function getListParams() {
        $result = parent::getListParams();
        $result['orderby'] = $this->request->get('orderby', 'created_on');
        $result['orderdir'] = $this->request->get('orderdir', 'desc');
        return $result;
    }

    protected function getFilterWhere() {
        $search = $this->filterForm->getValue('search');
        $result = [];
        if ($search) {
            $searchLike = '%'.$search.'%';
            $result[] = ['or', [
                ['name', 'like', $searchLike],
                ['email', 'like', $searchLike]
            ]];
        }
        return $result;
    }

    protected function saveForm(Record $record, Form $form) {
        return null;
    }
}