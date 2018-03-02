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
        $this->responseLayout(':core/layout', ':user/profileView');
    }

    public function edit() {

    }

}