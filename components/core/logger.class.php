<?php

class Logger {
    
    const INFO = 0;
    const WARNING = 1;
    const ERROR = 2;

    protected $level;
    protected $path;

    public function __construct($level, $path) {
        $this->level = $level;
        $this->path = $path;
        set_error_handler([$this, 'handleError'], E_ALL | E_STRICT);
    }

    public function info($message) {
        if ($this->level <= Logger::INFO) {
            $this->log('INFO', $message);
        }
    }

    public function warning($message) {
        if ($this->level <= Logger::WARNING) {
            $this->log('WARNING', $message);
        }
    }

    public function error($message) {
        if ($this->level <= Logger::ERROR) {
            $this->log('ERROR', $message);
        }
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
        $message = $errstr." (".$errno.")\r\nFile: ".$errfile."\r\nLine: ".$errline."\r\n";
        $this->error($message);
    }

    protected function log($label, $message) {
        $text = date('Y-m-d H:i:s').' ['.$label.'] '.$message."\r\n";
        $result = file_put_contents($this->path, $text, FILE_APPEND | LOCK_EX);
        if ($result === false) {
            throw new Exception("Can't write to ".$this->path);
        }
    }

}