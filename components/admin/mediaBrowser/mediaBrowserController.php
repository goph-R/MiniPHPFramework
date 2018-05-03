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
        $folders = $this->mediaService->findByParentIdAndType($parentId, MediaService::TYPE_FOLDER);
        foreach ($folders as $folder) {
            $result[] = $folder->getAttributes();
        }
        $this->respondJson($result);        
    }
    
    public function files() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $parentId = $this->request->get('id');
        $result = [];
        $files = $this->mediaService->findByParentIdAndType($parentId, MediaService::TYPE_FILE);
        foreach ($files as $file) {
            $result[] = $file->getAttributes();
        }
        $this->respondJson($result);        
    }
    
    public function thumbnail() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $id = $this->request->get('id');
        $media = $this->mediaService->findById($id);
        if (!$media) {
            return $this->respond404();
        }        
        $oneYearInSecs = 60*60*24*365;
        $path = $this->mediaService->createThumbnail($media, 90, 90);
        $expTime = gmdate('D, d M Y H:i:s', time() + $oneYearInSecs).' GMT';
        $this->response->setHeader('Content-Type', 'image/jpeg');
        $this->response->setHeader('Content-Length', filesize($path));
        $this->response->setHeader('Expires', $expTime);
        $this->response->setHeader('Pragma', 'cache');
        $this->response->setHeader('Cache-Control', 'max-age=$oneYearInSecs');
        $this->response->setContent(file_get_contents($path));
    }
    
}
