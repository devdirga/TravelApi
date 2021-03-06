<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\TicketStatusResponseParser;
use Travel\Libraries\MTI;
use DateTime;

class TicketStatusController extends APIController
{
    protected $invoking = "Ticket Status Flight";

    public function indexAction()
    {
        $this->setMTI(MTI::PAYMENT);
        $this->setProductCode("TPQZ");

        $message = new FlightMessage($this);

        $message->set(FlightMessage::FIELD_FLIGHT_STEP, "ISSUE");
        $message->set(FlightMessage::FIELD_MASKAPAI, "TPQZ");
        $message->set(FlightMessage::FIELD_PROCESS, "07");

        $startDate = DateTime::createFromFormat('Y-m-d', $this->request->startDate);
        $endDate = DateTime::createFromFormat('Y-m-d', $this->request->endDate);

        $message->set(FlightMessage::FIELD_CLASSNAME2, $startDate->format('d-m-Y'));
        $message->set(FlightMessage::FIELD_CLASSNAME3, $endDate->format('d-m-Y'));

        $this->sendToCore($message);

        TicketStatusResponseParser::instance()->parse($message)->into($this);
    }
}