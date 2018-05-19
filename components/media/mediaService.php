<?php

class MediaService {

    const DEFAULT_THUMB_QUALITY = 80;
    const DEFAULT_THUMB_MAXIMUM_SIZE = 600;
    const DEFAULT_THUMB_SIZE = 90;

    const TYPE_FOLDER = 1;
    const TYPE_FILE = 2;
    
    private static $functionByMime = [
        'image/png'   => 'png',
        'image/jpg'   => 'jpeg',
        'image/jpeg'  => 'jpeg',
        'image/pjpeg' => 'jpeg',
        'image/gif'   => 'gif'
    ];

    private static $mimeByExtension = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'html' => 'text/html',
        'mp4'  => 'video/mp4',
        'mp3'  => 'audio/mpeg',
        'ogg'  => 'audio/ogg'
    ];
    
    /**
     * @var Table
     */
    private $table;
    
    /**
     * @var User
     */
    private $user;
    
    /**
     * @var Uploader
     */
    private $uploader;

    private $uploadDir;
    private $thumbQuality;
    private $maxThumbSize;
    private $defaultThumbSize;

    public function __construct() {
        $im = InstanceManager::getInstance();
        $config = $im->get('config');
        $tableFactory = $im->get('mediaTableFactory');
        $this->user = $im->get('user');
        $this->uploader = $im->get('uploader');
        $this->table = $tableFactory->createMedia();
        $this->uploadDir = $this->uploader->getDirectoryPath();
        $this->thumbQuality = $config->get('media.thumb_quality', self::DEFAULT_THUMB_QUALITY);
        $this->maxThumbSize = $config->get('media.maximum_thumb_size', self::DEFAULT_THUMB_MAXIMUM_SIZE);
        $this->defaultThumbSize = $config->get('media.default_thumb_size', self::DEFAULT_THUMB_SIZE);
    }

    public function getDefaultThumbSize() {
        return $this->defaultThumbSize;
    }

    public function getMimeByExtension($extension) {
        $extension = mb_strtolower($extension);
        if (isset(self::$mimeByExtension[$extension])) {
            return self::$mimeByExtension[$extension];
        }
        return 'text/plain';
    }
    
    /**
     * @param int $id
     * @return Record
     */
    public function findFolderById($id) {
        return $this->table->findOne(null, [
            'where' => [
                ['id', '=', $id],
                ['type', '=', self::TYPE_FOLDER]
            ]
        ]);        
    }
    
    /**
     * @param int $parentId
     * @param string $fullName
     * @return Record
     */
    public function findActiveByParentIdAndFullName($parentId, $fullName) {
        $result = $this->getNameAndExtension($fullName);
        return $this->table->findOne(null, [
            'where' => [
                ['parent_id', '=', $parentId],
                [['LOWER(name)'], '=', mb_strtolower($result['name'])],
                [['LOWER(extension)'], '=', mb_strtolower($result['extension'])],
                ['deleted', '=', false]
            ]
        ]);        
    }    
    
    /**
     * @param int
     * @return Record[]
     */
    public function findActiveByParentIdAndType($parentId, $type) {
        return $this->table->find(null, [
            'where' => [
                ['parent_id', '=', $parentId],
                ['type', '=', $type],
                ['deleted', '=', false]
            ],
            'order' => ['name' => 'asc']
        ]);
    }
    
    private function findPathByParentId(&$path, $parentId) {
        $parent = $this->findById($parentId);
        $path[] = $parent;
        if ($parent->get('parent_id') != null) {
            $this->findPathByParentId($path, $parent->get('parent_id'));
        }        
    }
        
    public function findPathByFile($file) {
        $path = [];
        $this->findPathByParentId($path, $file->get('parent_id'));
        return array_reverse($this->path);
    }
    
    /**
     * @param int
     * @return Record
     */    
    public function findById($id) {
        return $this->table->findOne(null, [
            'where' => [
                ['id', '=', $id]
            ]
        ]);        
    }
    
    public function getFilePath($hash) {        
        $firstDir = mb_substr($hash, 0, 2).'/';
        $secondDir = mb_substr($hash, 2, 2).'/';
        return $this->uploadDir.$firstDir.$secondDir.$hash;
    }
    
    private function createThumbPath($hash, $width, $height) {
        $parts = ['thumbs', $width.'x'.$height, mb_substr($hash, 0, 2), mb_substr($hash, 2, 2)];
        $path = $this->uploadDir;
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
    public function createThumbnail(Record $media, $width, $height) {
        if ($width > $this->maxThumbSize || $width < 1) {
            $width = $this->defaultThumbSize;
        }
        if ($height > $this->maxThumbSize || $height < 1) {
            $height = $this->defaultThumbSize;
        }
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
    
    public function createFolder($parentId, $name) {
        $record = $this->table->createRecord([
            'parent_id'  => $parentId,
            'name'       => $name,            
            'extension'  => '',
            'type'       => self::TYPE_FOLDER,
            'user_id'    => $this->user->get('id'),
            'created_on' => time(),
            'hash'       => ''
        ]);
        $record->save();
    }
    
    public function getNameAndExtension($fullName) {
        $name = $fullName;
        $extension = '';
        $pos = mb_strrpos($fullName, '.');
        if ($pos) {
            $name = mb_substr($fullName, 0, $pos);
            $extension = mb_substr($fullName, $pos+1, mb_strlen($fullName));
        }
        return ['name' => $name, 'extension' => $extension];
    }
    
    public function rename($id, $fullName) {
        $record = $this->findById($id);
        $record->setAll(['name', 'extension'], $this->getNameAndExtension($fullName));
        $record->save();
    }
    
    public function findAllActiveChildren(&$children, $parentId) {
        $records = $this->table->find(null, [
            'where' => [
                ['parent_id', '=', $parentId],
                ['deleted', '=', false]
            ]
        ]);
        foreach ($records as $record) {
            $children[] = $record;
            if ($record->get('type') == self::TYPE_FOLDER) {
                $this->findAllActiveChildren($children, $record->get('id'));                
            }
        }
    }
    
    public function delete($id) {
        $children = [];
        $this->findAllActiveChildren($children, $id);
        $children[] = $this->findById($id);
        foreach ($children as $child) {
            $child->set('deleted', true);
            $child->save();
        }
    }
    
    public function upload($inputName, $parentId) {
        $hash = md5(microtime(true).$this->user->get('id').date('YmdHis')); // TODO: make better unique hash
        $targetPath = $this->getFilePath($hash);
        $this->uploader->upload($inputName, $targetPath);
        $fullName = $this->getNameAndExtension($this->uploader->getBaseName($inputName));
        $record = $this->table->createRecord([
            'parent_id'  => $parentId,
            'name'       => $fullName['name'],            
            'extension'  => $fullName['extension'],
            'type'       => self::TYPE_FILE,
            'user_id'    => $this->user->get('id'),
            'created_on' => time(),
            'hash'       => $hash
        ]);
        $record->save();
        return $record;
    }
    
}
