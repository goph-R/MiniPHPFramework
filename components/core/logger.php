<?php

class Logger {
    
    const INFO = 1;
    const WARNING = 2;
    const ERROR = 3;

    private static $levelMap = [
        'info' => self::INFO,
        'warning' => self::WARNING,
        'error' => self::ERROR
    ];

    protected $level;
    protected $path;
    protected $enviroment;

    public function __construct(Config $config) {
        $this->level = @self::$levelMap[$config->get('logger.level')];
        $this->path = $config->get('logger.path');
        $this->enviroment = $config->getEnvironment();
        set_error_handler([$this, 'handleError']);
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
        // TODO: better solution
        if ($this->enviroment == 'dev') {
            print str_replace("\n", "<br>", $message);
        }
    }

    protected function log($label, $message) {
        $text = date('Y-m-d H:i:s').' ['.$label.'] '.$message."\r\n";
        $result = file_put_contents($this->path, $text, FILE_APPEND | LOCK_EX);
        if ($result === false) {
            throw new RuntimeException("Can't write to ".$this->path);
        }
    }

}