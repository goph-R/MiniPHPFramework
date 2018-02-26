<?php

class ProfileController extends Controller {

    public function index() {
        // TODO: check permission
        $service = $this->im->get('userService');
        $record = $service->findById($this->request->get('id'));
        if (!$record) {
            // TODO: 404
        }
        $this->view->set('record', $record);
        $this->responseLayout('components/core/templates/layout', 'components/user/templates/profileView');
    }

    public function edit() {

    }

}