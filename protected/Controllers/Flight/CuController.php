<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\CUResponseParser;
use Travel\Libraries\MTI;

class CuController extends APIController
{
    protected $invoking = "CU Flight";

    public function indexAction()
    {
        $this->setMTI(MTI::PAYMENT);
        $this->setProductCode($this->request->airline);

        $message = new FlightMessage($this);

        $message->set(FlightMessage::FIELD_FLIGHT_STEP, "ISSUE");
        $message->set(FlightMessage::FIELD_MASKAPAI, $this->request->airline);
        $message->set(FlightMessage::FIELD_PROCESS, "04");
        $message->set(FlightMessage::FIELD_RUTE, "0");
        $message->set(FlightMessage::FIELD_CLASSNAME1, $this->request->bookingCode);
        $message->set(FlightMessage::FIELD_CLASSNAME2, $this->request->airline);

        $message->set(FlightMessage::FIELD_TRANSIT_VIA_GO, "0");
        $message->set(FlightMessage::FIELD_TRANSIT_VIA_BACK, "0");

        $message->set(FlightMessage::FIELD_ID_PEL1, "TPSW");

        $this->sendToCore($message);

        CUResponseParser::instance()->parse($message)->into($this);
    }
}