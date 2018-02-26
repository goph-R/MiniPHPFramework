<?php 

class HiddenInput extends TextInput {
    public function create() {
        $this->type = 'hidden';
    }
}
