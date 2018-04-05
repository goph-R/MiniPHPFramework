<?php

class ClassLoader {

    private static $files = [];
    private static $rootPath;

    public static function storeFiles() {
        if (self::$files) {
            return;
        }
        $directory = new RecursiveDirectoryIterator(self::$rootPath, RecursiveDirectoryIterator::SKIP_DOTS);
        $fileIterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($fileIterator as $file) {
            if (substr($file->getFilename(), -4) == '.php' && $file->isReadable()) {
                self::$files[] = $file;
            }
        }
    }

    public static function load($className) {
        self::storeFiles();
        $filename = strtolower($className.'.php');
        foreach (self::$files as $file) {
            if (strtolower($file->getFilename()) == $filename) {
                include_once $file->getPathname();
                break;
            }
        }
    }

    public static function initialize($rootPath=null) {
        if ($rootPath === null) {
            $rootPath = __DIR__.'/..';
        }
        self::$rootPath = $rootPath;
        spl_autoload_register(['ClassLoader', 'load']);
    }

}


