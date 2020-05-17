<?php

namespace Travel\TravelBus;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\TravelBus\OriginResponseParser;

class OriginController extends APIController
{
    protected $invoking = "Origin TravelBus";

    public function indexAction()
    {
        $message = new TravelBusMessage($this);

        $this->sendToCore($message);

        OriginResponseParser::instance()->parse($message)->into($this);
    }
}