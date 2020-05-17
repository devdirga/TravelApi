<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\ConfigurationResponseParser;
use Travel\Libraries\MTI;

class ConfigurationController extends APIController
{
    protected $invoking = "Configuration Flight";

    public function indexAction()
    {
        $this->setMTI(MTI::RESERVATION);
        $this->setProductCode("AIRPORT");

        $message = new FlightMessage($this);

        $message->set(FlightMessage::FIELD_FLIGHT_STEP, "AIRPORT");
        $message->set(FlightMessage::FIELD_PROCESS, "01");
        $message->set(FlightMessage::FIELD_VIA, "WEB");

        $this->sendToCore($message);

        ConfigurationResponseParser::instance()->parse($message)->into($this);
    }
}