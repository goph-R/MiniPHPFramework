<?php

class BooleanColumn extends Column {
	public function convert($value) {
		return $value ? 1 : 0;
	}
}
