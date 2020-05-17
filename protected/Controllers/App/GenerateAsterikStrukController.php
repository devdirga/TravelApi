<?php

namespace Travel\App;

use Travel\Libraries\Parser\App\GenerateAsterikStrukResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;

class GenerateAsterikStrukController extends APIController
{
    public $requestGET;

    protected $invoking = "Generate Asterik Struk App";

    public function indexAction()
    {

        $message = new AppMessage($this);

        GenerateAsterikStrukResponseParser::instance()->parse($message)->into($this);
    }
}