<?php

namespace Travel\Libraries\Models;

class Outlet extends BaseModel
{
    public $idOutlet;
    public $balance;

    public function initialize()
    {
        $this->setSchema('fmss');

        $this->setSource("mt_outlet");
    }

    public static function take($outletId, $field = null)
    {
        $outlet = self::findFirst([
            "conditions" => "idOutlet = ?1",
            "bind" => [1 => $outletId]
        ]);

        if ($field == null) {
            return $outlet;
        } else {
            return $outlet->$field;
        }
    }
}