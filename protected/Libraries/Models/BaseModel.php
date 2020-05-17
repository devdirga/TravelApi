<?php

namespace Travel\Libraries\Models;

use Phalcon\Mvc\Model;
use Phalcon\Text;

class BaseModel extends Model
{
    public function columnMap()
    {
        $columns = $this->getModelsMetaData()->getAttributes($this);
        $map = [];

        foreach ($columns as $column) {
            $map[$column] = lcfirst(Text::camelize($column));
        }

        return $map;
    }
}