<?php

class BooleanColumnView extends ColumnView {

    public function fetch($record) {
        return $record->get($this->columnName) ? 'Yes' : 'No';
    }

}