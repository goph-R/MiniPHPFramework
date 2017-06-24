<?php

class TextInput extends Input {

	protected $type = 'text';

	public function fetch() {
		$result = '<input type="'.$this->type.'"';
		$result .= ' name="'.$this->getName().'"';
		$result .= ' value="'.$this->view->escape($this->getValue()).'"';
		$result .= ' class="'.$this->getClasses().'">';
		return $result;
	}

}