<?php

class MediaBrowserController extends Controller {
        
    /**
     * @var MediaService
     */
    private $mediaService;
    
    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->config = $im->get('config');
        $this->mediaService = $im->get('mediaService');
    }
    
    public function index() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $defaultFolderId = $this->config->get('mediabrowser.default_folder_id', 1);
        $folder = $this->mediaService->findFolder($defaultFolderId);
        if (!$folder) {
            throw new RuntimeException("Can't find the default folder: $defaultFolderId");
        }        
        $this->view->set('folder', $folder->getAttributes());
        $this->respondView(':browser/browser');
    }
    
    public function folders() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $parentId = $this->request->get('id');
        $result = [];
        $folders = $this->mediaService->findFolders($parentId);
        foreach ($folders as $folder) {
            $result[] = $folder->getAttributes();
        }
        $this->respondJson($result);
        
    }
    
}
