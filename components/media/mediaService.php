<?php

class MediaService {
    
    const TYPE_FOLDER = 1;
    const TYPE_FILE = 2;
    
    const FUNCTION_BY_MIME = [
        'image/png'   => 'png',
        'image/jpg'   => 'jpeg',
        'image/jpeg'  => 'jpeg',
        'image/pjpeg' => 'jpeg',
        'image/gif'   => 'gif'
    ];
    
    /**
     * @var Table
     */
    private $table;
    
    private $mediaPath;
    private $thumbQuality;
    private $path;
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $config = $im->get('config');
        $tableFactory = $im->get('mediaTableFactory');
        $this->table = $tableFactory->createMedia();
        $defaultPath = $config->get('application.path').'media/';
        $this->mediaPath = $config->get('media.path', $defaultPath);        
        $this->thumbQuality = $config->get('media.thumb_quality', 80);
    }
    
    /**
     * @param int $id
     * @return Record
     */
    public function findFolder($id) {
        return $this->table->findOne(null, [
            'where' => [
                ['id', '=', $id],
                ['type', '=', self::TYPE_FOLDER]
            ]
        ]);        
    }
    
    /**
     * @param int
     * @return Record[]
     */
    public function findByParentIdAndType($parentId, $type) {
        return $this->table->find(null, [
            'where' => [
                ['parent_id', '=', $parentId],
                ['type', '=', $type]
            ],
            'order' => ['name' => 'asc']
        ]);
    }
    
    private function findPathByParentId($parentId) {
        $parent = $this->findById($parentId);
        $this->path[] = $parent;
        if ($parent->get('parent_id') != null) {
            $this->findPathByParentId($parent->get('parent_id'));
        }        
    }
        
    public function findPathByFile($file) {
        $this->path = [];
        $this->findPathByParentId($file->get('parent_id'));
        return array_reverse($this->path);
    }
    
    /**
     * @param int
     * @return Record[]
     */    
    public function findById($id) {
        return $this->table->findOne(null, [
            'where' => [
                ['id', '=', $id]
            ]
        ]);        
    }
    
    /**
     * @param Record
     * @param int
     * @param int
     * @return string
     */
    public function createThumbnail($media, $width, $height) {
        $hash = $media->get('hash');
        $firstDir = mb_substr($hash, 0, 2).'/';
        $secondDir = mb_substr($hash, 2, 2).'/';
        $basename = $firstDir.$secondDir.$hash;
        $filePath = $this->mediaPath.$basename;
        $thumbsPath = $this->mediaPath.'/thumbs/';
        $thumbPath = $thumbsPath.$basename;
        if (file_exists($thumbPath)) {
            return $thumbPath;
        }
        $this->createThumbnailDirectories($thumbsPath, $firstDir, $secondDir);
        $srcImg = $this->createImage($filePath);
        $dstImg = $this->resizeImageKeepRatio($srcImg, $width, $height);
        imagejpeg($dstImg, $thumbPath, $this->thumbQuality);
        return $thumbPath;
    }
    
    private function resizeImageKeepRatio($srcImg, $width, $height) {
        $w = imagesx($srcImg);
        $h = imagesy($srcImg);
        if ($w > $h) {
            $tw = $width;
            $th = $h/$w * $width;
        } else {
            $tw = $w/$h * $height;
            $th = $height;            
        }
        $dstImg = imagecreatetruecolor($tw, $th);
        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $tw, $th, $w, $h);
        return $dstImg;
    }
    
    private function createImage($path) {
        $size = getimagesize($path);
        $mime = isset($size['mime']) ? $size['mime'] : 'Unknown';
        if (!isset(self::FUNCTION_BY_MIME[$mime])) {
            throw new RuntimeException("Can't find a thumbnail creator function for MIME: $mime");
        }
        $func = 'imagecreatefrom'.self::FUNCTION_BY_MIME[$mime];
        $srcImg = $func($path);
        if (!$srcImg) {
            throw new RuntimeException("Can't create an image from: $path");
        }
        return $srcImg;
    }
    
    private function createThumbnailDirectories($thumbsPath, $firstDir, $secondDir) {
        if (!file_exists($thumbsPath)) {
            mkdir($thumbsPath, 0755);
        }
        if (!file_exists($thumbsPath.$firstDir)) {
            mkdir($thumbsPath.$firstDir, 0755);
        }
        if (!file_exists($thumbsPath.$firstDir.$secondDir)) {
            mkdir($thumbsPath.$firstDir.$secondDir, 0755);
        }
    }
    
}
