<?php

class MediaBrowserController extends Controller {
        
    /**
     * @var MediaService
     */
    private $mediaService;
    
    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->mediaService = $im->get('mediaService');
    }
    
    public function index() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $defaultFolderId = $this->config->get('mediabrowser.default_folder_id', 1);
        $folder = $this->mediaService->findFolderById($defaultFolderId);
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
        $folders = $this->mediaService->findActiveByParentIdAndType($parentId, MediaService::TYPE_FOLDER);
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
        $files = $this->mediaService->findActiveByParentIdAndType($parentId, MediaService::TYPE_FILE);
        foreach ($files as $file) {
            $result[] = $file->getAttributes();
        }
        $this->respondJson($result);        
    }
    
    public function createFolder() {
        $parentId = $this->request->get('parent_id');
        $name = trim($this->request->get('name'));
        if ($this->nameIsOk($parentId, $name)) {
            $this->mediaService->createFolder($parentId, $name);
            $this->respondJson('ok');
        }
    }
    
    public function rename() {        
        $name = trim($this->request->get('name'));
        $id = $this->request->get('id');
        $record = $this->mediaService->findById($id);
        if ($this->nameIsOk($record->get('parent_id'), $name)) {
            $this->mediaService->rename($id, $name);
            $this->respondJson('ok');
        }
    }
    
    private function nameIsOk($parentId, $name) {
        if (!$this->user->hasPermission('admin')) {
            return false;
        }
        $folder = $this->mediaService->findById($parentId);
        if (!$folder) {
            return $this->respond404();
        } else if ($name == '') {
            return $this->respondJson(['error' => 'Please provide a name!']);
        } else if ($this->mediaService->findActiveByParentIdAndFullName($parentId, $name)) {
            return $this->respondJson(['error' => 'The name exists.']);
        }
        return true;        
    }

    public function delete() {
        if (!$this->user->hasPermission('admin')) {
            return false;
        }
        $id = $this->request->get('id');
        $this->mediaService->delete($id);
    }
    
    public function upload() {
        if (!$this->user->hasPermission('admin')) {
            return false;
        }
        $parentId = $this->request->get('parent_id');
        try {
            $this->mediaService->upload('file', $parentId);
        } catch (RuntimeException $e) {
            return $this->respondJson(['error' => $e->getMessage()]);
        }
        return $this->respondJson('ok');
    }
}
