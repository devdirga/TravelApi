<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\BookingInfoResponseParser;
use Travel\Libraries\MTI;

class BookingInfoController extends APIController
{
    protected $invoking = "Booking Info Flight";

    public function indexAction()
    {
        $this->setMTI(MTI::RESERVATION);
        $this->setProductCode($this->request->airline);

        $message = new FlightMessage($this);

        $message->set(FlightMessage::FIELD_FLIGHT_STEP, "BOOKING");
        $message->set(FlightMessage::FIELD_MASKAPAI, $this->request->airline);
        $message->set(FlightMessage::FIELD_RUTE, "0");
        $message->set(FlightMessage::FIELD_PROCESS, "05");

        $message->set(FlightMessage::FIELD_CITY_ORIGIN, $this->request->departure);
        $message->set(FlightMessage::FIELD_CITY_DESTINATION, $this->request->arrival);

        $message->set(FlightMessage::FIELD_TRX_ID, $this->request->transactionId);
        $message->set(FlightMessage::FIELD_ID_PEL1, "TPSW");
        $message->set(FlightMessage::FIELD_JENIS_STRUK, "0");

        $this->sendToCore($message);

        BookingInfoResponseParser::instance()->parse($message)->into($this);
    }
}