<?php

namespace Travel\TravelBus;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\TravelBus\ChooseSeatResponseParser;

class ChooseSeatController extends APIController
{
    protected $invoking = "Choose Seat TravelBus";

    public function indexAction()
    {
        $message = new TravelBusMessage($this);

        $this->sendToCore($message);

        ChooseSeatResponseParser::instance()->parse($message)->into($this);
    }
}