<?php

class Uploader {

    const DEFAULT_MAXIMUM_SIZE = 2097152; // 2MB
    const DEFAULT_PATH = 'media/';

    /**
     * @var Request
     */
    private $request;
    
    private $maxSize;
    private $dirPath;
    
    
    public function __construct() {
        $im = InstanceManager::getInstance();
        $config = $im->get('config');
        $this->request = $im->get('request');
        $this->maxSize = $config->get('upload.maximum_size', self::DEFAULT_MAXIMUM_SIZE); // default: 2MB
        $defaultPath = $config->get('application.path', '').self::DEFAULT_PATH;
        $this->dirPath = $config->get('media.path', $defaultPath);
    }

    public function getMaximumSize() {
        return $this->maxSize;
    }

    public function getDirectoryPath() {
        return $this->dirPath;
    }
    
    public function getBaseName($inputName) {
        return basename($this->request->getFileName($inputName));        
    }
    
    public function upload($inputName, $targetPath) {
        $this->checkUploadErrors($inputName);
        $tempPath = $this->request->getFileTempPath($inputName);
        if (filesize($tempPath) > $this->maxSize) {
            throw new RuntimeException('Exceeded filesize limit.');
        }
        $destPath = str_replace('{basename}', $this->getBaseName($inputName), $targetPath);
        $this->createDirectories($destPath);
        if (!move_uploaded_file($tempPath, $destPath)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }
    }
    
    private function checkUploadErrors($inputName) {
        $error = $this->request->getFileError($inputName);
        if ($error === null || is_array($error)) {
            throw new RuntimeException('Invalid parameters.');
        }
        switch ($error) {
            case UPLOAD_ERR_OK:
                break;            
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }        
    }
    
    private function createDirectories($destPath) {
        $destDir = dirname($destPath);
        $currentDir = $this->dirPath.'/';
        $baseDir = str_replace($this->dirPath, '', $destDir);
        $dirs = explode('/', $baseDir);        
        foreach ($dirs as $dir) {
            $currentDir .= $dir.'/';
            if (!file_exists($currentDir) && !mkdir($currentDir, 0755)) {
                throw new RuntimeException("Couldn't create directory: $currentDir");
            }
        }        
    }
    
}
