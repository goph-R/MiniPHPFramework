<?php

class SeparatorInput extends Input {

    public function fetch() {
        return $this->defaultValue;
    }

}