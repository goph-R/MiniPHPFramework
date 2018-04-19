<?php

class PageController extends Controller {

    /**
     * @var PageService
     */
    private $pageService;

    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->pageService = $im->get('pageService');
    }

    public function index() {
        $locale = $this->request->get('locale');
        $name = $this->request->get('name');
        $pageRecord = $this->pageService->findByLocaleAndName($locale, $name);
        if (!$pageRecord) {
            return $this->respond404();
        }
        $this->view->set('pageRecord', $pageRecord);
        return $this->respondLayout(':core/layout', ':page/view');
    }
    
}
