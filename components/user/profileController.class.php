<?php

class ProfileController extends UserController {

    public function index() {
        // TODO: check permission
        $record = $this->userService->findById($this->request->get('id'));
        if (!$record) {
            // TODO: 404
        }
        $this->view->set('record', $record);
        $this->responseLayout(':core/layout', ':user/profileView');
    }

    public function edit() {

    }

}