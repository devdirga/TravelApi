<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\BookingListResponseParser;
use Travel\Libraries\MTI;

class BookingListController extends APIController
{
    protected $invoking = "Booking List Flight";

    public function indexAction()
    {
        $this->setMTI(MTI::RESERVATION);
        $this->setProductCode("AIRPORT");

        $message = new FlightMessage($this);

        $message->set(FlightMessage::FIELD_FLIGHT_STEP, "BOOKING");
        $message->set(FlightMessage::FIELD_MASKAPAI, "AIRPORT");
        $message->set(FlightMessage::FIELD_RUTE, "0");
        $message->set(FlightMessage::FIELD_PROCESS, "04");

        $message->set(FlightMessage::FIELD_CLASSNAME3, date("d-m-Y"));
        $message->set(FlightMessage::FIELD_CLASSNAME4, date("d-m-Y"));

        $message->set(FlightMessage::FIELD_ID_PEL1, "TPSW");
        $message->set(FlightMessage::FIELD_JENIS_STRUK, "0");

        $this->sendToCore($message);

        BookingListResponseParser::instance()->parse($message)->into($this);
    }
}