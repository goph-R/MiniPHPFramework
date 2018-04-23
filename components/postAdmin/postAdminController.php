<?php

class PostAdminController extends AdminController {

    /**
     * @var PostTableFactory
     */
    private $postTableFactory;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->postTableFactory = $im->get('postTableFactory');
        $this->indexRoute = 'admin/posts';
        $this->indexTitle = $this->translation->get('postAdmin', 'posts');
        $this->editRoute = 'admin/post/edit';
        $this->editTitle = $this->translation->get('postAdmin', 'edit_post');
        $this->addRoute = 'admin/post/add';
        $this->addTitle = $this->translation->get('postAdmin', 'add_post');
    }

    protected function createTable() {
        return $this->postTableFactory->createPost();
    }

    protected function createColumnViews() {
        return [
            new ColumnView('id', 'ID', 'right'),
            new ColumnView('locale', $this->translation->get('postAdmin', 'locale')),
            new ColumnView('title', $this->translation->get('postAdmin', 'title'), 'left', '100%'),
            new DateColumnView('created_on', $this->translation->get('postAdmin', 'created_on')),
            new BooleanColumnView('active', $this->translation->get('core', 'active'), 'center')
        ];
    }

    protected function createFormFactory() {
        return new postAdminFormFactory();
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
                ['title', 'like', $searchLike]
            ]];
        }
        return $result;
    }

    protected function saveForm(Record $record, Form $form) {
        $fields = ['title', 'lead', 'content', 'active'];
        $values = $form->getValues();
        if ($record->isNew()) {
            $fields = array_merge($fields, ['user_id', 'created_on']);
            $values['user_id'] = $this->user->get('id');
            $values['created_on'] = time();
        }
        $record->setAll($fields, $values);
        return $record;
    }
}