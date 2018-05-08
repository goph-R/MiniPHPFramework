<?php

class MediaController extends Controller {
    
    /**
     * @var MediaService
     */
    private $mediaService;
    
    public function __construct() {
        parent::__construct();
        $im = InstanceManager::getInstance();
        $this->mediaService = $im->get('mediaService');
    }
        
    public function thumbnail() {
        if (!$this->user->hasPermission('admin')) {
            return $this->redirect();
        }
        $id = $this->request->get('id');
        $width = $this->request->get('width', 90);
        $height = $this->request->get('height', 90);
        if ($width > 1000 || $width < 10) {
            $width = 90;
        }
        if ($height > 1000 || $height < 10) {
            $height = 90;
        }
        $media = $this->mediaService->findById($id);
        if (!$media) {
            return $this->respond404(); // TODO: image not found picture
        } else {
            $path = $this->mediaService->createThumbnail($media, $width, $height);
        }
        $this->respondCachableFile(file_get_contents($path), filesize($path), 'image/jpeg');
    }
    
    public function get() {
        $id = $this->request->get('id');
        $media = $this->mediaService->findById($id);
        if (!$media || $media->get('deleted') || !$media->get('hash')) {
            return $this->respond404();
        } else {
            
        }
        $path = $this->mediaService->getFilePath($media->get('hash'));
        $this->respondCachableFile(file_get_contents($path), filesize($path));
    }
}
