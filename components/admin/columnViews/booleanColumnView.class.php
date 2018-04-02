<?php

class BooleanColumnView extends ColumnView {

    public function fetch($record) {
        return $record->get($this->columnName) ? '<i class="fa fa-check"></i>' : '';
    }

}