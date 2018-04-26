<?php

class MediaBrowserController extends Controller {
    
    public function index() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $this->respondView(':browser/browser');
    }
    
}
