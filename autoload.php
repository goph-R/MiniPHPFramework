<?php

function __autoload($className) {
	$directory = new RecursiveDirectoryIterator(__DIR__.'/components', RecursiveDirectoryIterator::SKIP_DOTS);
	$fileIterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);
	$filename = $className.'.class.php';
	foreach ($fileIterator as $file) {
		if (strtolower($file->getFilename()) === strtolower($filename)) {
			if ($file->isReadable()) {
				include_once $file->getPathname();
			}
			break;
		}
	}
}
