<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\SlidesResponseParser;

class SlidesController extends APIController
{
    protected $invoking = "Slides App";

    public function indexAction()
    {
        $message = new AppMessage($this);

        SlidesResponseParser::instance()->parse($message)->into($this);
    }
}