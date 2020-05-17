<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\ListBaggageResponseParser;

class ListBaggageController extends APIController
{
    protected $invoking = "Baggage List App";

    public function indexAction()
    {
        $message = new AppMessage($this);

        ListBaggageResponseParser::instance()->parse($message)->into($this);
    }
}