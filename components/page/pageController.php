<?php

class PageController extends Controller {
    
    public function index() {
        $locale = $this->request->get('locale');
        $name = $this->request->get('name');
        $im = InstanceManager::getInstance();
        $pageService = $im->get('pageService');
        $pageRecord = $pageService->findByLocaleAndName($locale, $name);
        if (!$pageRecord) {
            return $this->response404();
        }
        $this->view->set('pageRecord', $pageRecord);
        return $this->responseLayout(':core/layout', ':page/view');
    }
    
}
