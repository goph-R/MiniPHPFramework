<?php

class TextInput extends Input {

	protected $type = 'text';

	public function fetch() {
		$classes = $this->classes;
		if ($this->hasError()) {
			$classes[] = 'error';
		}
		$result = '<input type="'.$this->type.'"';
		$result .= ' name="'.$this->getName().'"';
		$result .= ' value="'.$this->view->escape($this->getValue()).'"';
		$result .= ' class="'.join($classes, ' ').'">';
		return $result;
	}

}