<?php

class DateColumnView extends ColumnView {

    public function fetch(Record $record) {
        return $this->removeBreaks(date('Y-m-d H:i', $record->get($this->columnName)));
    }

}