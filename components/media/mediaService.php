<?php

class MediaService {
    
    const TYPE_FOLDER = 1;
    const TYPE_FILE = 2;
    
    private static $functionByMime = [
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
    
    /**
     * @var User
     */
    private $user;
    
    private $mediaPath;
    private $thumbQuality;
    private $path;
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $config = $im->get('config');
        $tableFactory = $im->get('mediaTableFactory');
        $this->user = $im->get('user');
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
     * @param int $parentId
     * @param string $name
     * @return Record
     */
    public function findByParentIdAndName($parentId, $name) {
        return $this->table->findOne(null, [
            'where' => [
                ['parent_id', '=', $parentId],
                [['LOWER(name)'], '=', mb_strtolower($name)]
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
    
    private function getFilePath($hash) {        
        $firstDir = mb_substr($hash, 0, 2).'/';
        $secondDir = mb_substr($hash, 2, 2).'/';
        return $this->mediaPath.$firstDir.$secondDir.$hash;
    }
    
    private function createThumbPath($hash, $width, $height) {
        $parts = ['thumbs', $width.'x'.$height, mb_substr($hash, 0, 2), mb_substr($hash, 2, 2)];
        $path = $this->mediaPath;
        foreach ($parts as $part) {
            $path .= $part.'/';
            if (!file_exists($path)) {
                mkdir($path, 0755);
            }
        }
        $path .= $hash;
        return $path;
    }
    
    /**
     * @param Record
     * @param int
     * @param int
     * @return string
     */
    public function createThumbnail($media, $width, $height) {
        $hash = $media->get('hash');
        $filePath = $this->getFilePath($hash);
        $thumbPath = $this->createThumbPath($hash, $width, $height);
        if (file_exists($thumbPath)) {
            return $thumbPath;
        }
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
        if (!isset(self::$functionByMime[$mime])) {
            throw new RuntimeException("Can't find a thumbnail creator function for MIME: $mime");
        }
        $func = 'imagecreatefrom'.self::$functionByMime[$mime];
        $srcImg = $func($path);
        if (!$srcImg) {
            throw new RuntimeException("Can't create an image from: $path");
        }
        return $srcImg;
    }
    
    public function newFolder($parentId, $name) {
        $record = $this->table->createRecord([
            'parent_id' => $parentId,
            'name' => $name,            
            'extension' => '',
            'type' => self::TYPE_FOLDER,
            'user_id' => $this->user->get('id'),
            'created_on' => time(),
            'hash' => ''
        ]);
        $record->save();
    }
    
    public function renameFolder($id, $name) {
        $record = $this->findById($id);
        $record->set('name', $name);
        $record->save();
    }
    
}
