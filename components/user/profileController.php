<?php

class ProfileController extends UserController {

    public function index() {
        $record = $this->userService->findById($this->request->get('id'));
        if (!$record) {
            return $this->respond404();
        }
        $this->view->set('record', $record);
        return $this->respondLayout(':core/layout', ':user/profile');
    }

}