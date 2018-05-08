<?php

class Response {

    private $headers = [];
    private $content;

    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    public function setHeaders($values) {
        foreach ($values as $name => $value) {
            $this->setHeader($name, $value);
        }
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function send() {
        foreach ($this->headers as $name => $value) {
            if ($value) {
                header($name.': '.$value);
            }
        }
        echo $this->content;
    }
}
