<?php

class MessageFormFactory {
    
    /**
     * @return Form
     */
    public function createWriteForm() {
        $form = new Form();
        $form->addInput(['message', 'message'], new TextareaInput('message'));
        return $form;
    }
    
    
}

