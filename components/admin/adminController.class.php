<?php

class AdminController extends Controller {

    public function index() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        
        $this->responseLayout(':admin/layout', ':admin/index');
    }

}