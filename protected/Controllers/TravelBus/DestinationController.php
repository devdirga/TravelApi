<?php

namespace Travel\TravelBus;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\TravelBus\DestinationResponseParser;

class DestinationController extends APIController
{
    protected $invoking = "Destination TravelBus";

    public function indexAction()
    {
        $message = new TravelBusMessage($this);

        $this->sendToCore($message);

        DestinationResponseParser::instance()->parse($message)->into($this);
    }
}