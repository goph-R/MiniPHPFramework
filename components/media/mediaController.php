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
        $defaultThumbSize = $this->mediaService->getDefaultThumbSize();
        $id = $this->request->get('id');
        $width = $this->request->get('width', $defaultThumbSize);
        $height = $this->request->get('height', $defaultThumbSize);
        $media = $this->mediaService->findById($id);
        if ($this->isNotExist($media)) {
            return $this->respond404(); // TODO: image not found picture
        }
        $path = $this->mediaService->createThumbnail($media, $width, $height);
        $this->respondCacheableFile(file_get_contents($path), filesize($path), 'image/jpeg');
    }
    
    public function get() {
        $id = $this->request->get('id');
        $media = $this->mediaService->findById($id);
        if ($this->isNotExist($media)) {
            return $this->respond404();
        }
        $path = $this->mediaService->getFilePath($media->get('hash'));
        $mime = $this->mediaService->getMimeByExtension($media->get('extension'));
        $this->respondCacheableFile(file_get_contents($path), filesize($path), $mime);
    }

    private function isNotExist(Record $media) {
        return !$media || $media->get('deleted') || !$media->get('hash');
    }
}
