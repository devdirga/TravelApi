<?php

namespace Travel\App;

class ViewLogoController
{

    protected $invoking = "View Logo App";

    public function indexAction()
    {

        $url = "http://api.Travel.co.id/protected/Asset/Img/Travel.bmp.1";

        header("Content-type: image/bmp");



        $file = file_get_contents($url);

        echo $file;
    }
}