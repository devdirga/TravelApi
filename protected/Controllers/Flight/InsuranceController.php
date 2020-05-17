<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\InsuranceResponseParser;

class InsuranceController extends APIController
{
    protected $invoking = "Insurance Flight";

    public function indexAction()
    {
        $message = new FlightMessage($this);

        $this->sendToCore($message);

        InsuranceResponseParser::instance()->parse($message)->into($this);
    }
}