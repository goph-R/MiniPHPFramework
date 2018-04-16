<?php

class ProfileController extends UserController {

    public function index() {
        $record = $this->userService->findById($this->request->get('id'));
        if (!$record) {
            return $this->response404();
        }
        $this->view->set('record', $record);
        return $this->responseLayout(':core/layout', ':user/profile');
    }

}