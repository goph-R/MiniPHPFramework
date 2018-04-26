<?php

class MediaBrowserController extends Controller {
    
    public function index() {
        $this->respondView(':browser/browser');
    }
    
}
