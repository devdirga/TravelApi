<?php

namespace Travel\Libraries\Models;

class Slidesbf extends BaseModel
{

    public $idOutlet;
    public $balance;

    public function initialize()
    {
        $this->setSchema('fmss');

        $this->setSource("sbf_setting_slide_app_mobile");
    }
}