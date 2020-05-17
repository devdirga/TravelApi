<?php

namespace Travel\Libraries\Models;

class Slide extends BaseModel
{
    public $idOutlet;
    public $balance;

    public function initialize()
    {
        $this->setSchema('fmss');

        $this->setSource("ft_image_slide");
    }
}