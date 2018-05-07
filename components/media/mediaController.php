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
        }
        $oneYearInSecs = 60*60*24*365;
        $path = $this->mediaService->createThumbnail($media, $width, $height);
        $expTime = gmdate('D, d M Y H:i:s', time() + $oneYearInSecs).' GMT';
        $this->response->setHeader('Content-Type', 'image/jpeg');
        $this->response->setHeader('Content-Length', filesize($path));
        $this->response->setHeader('Expires', $expTime);
        $this->response->setHeader('Pragma', 'cache');
        $this->response->setHeader('Cache-Control', 'max-age=$oneYearInSecs');
        $this->response->setContent(file_get_contents($path));
    }    
}
