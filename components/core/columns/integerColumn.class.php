<?php

class IntegerColumn extends Column {	
	public function convert($value) {
		return (int)$value;
	}
}
