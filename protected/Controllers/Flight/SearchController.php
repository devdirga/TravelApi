<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\SearchResponseParser;
use Travel\Libraries\MTI;
use DateTime;

class SearchController extends APIController
{
    protected $invoking = "Search Flight";

    public function indexAction()
    {
        $this->setMTI(MTI::RESERVATION);
        $this->setProductCode($this->request->airline);

        $message = new FlightMessage($this);

        $step = "AVAIL";
        //$allow_loket = array("FA22044","FA32670","FT1013");
        if (isset($this->request->isLowestPrice) && $this->request->isLowestPrice == true && $this->request->airline == "TPJT") {
            $step = "AVAIL-CHEAPEST";
        }

        $message->set(FlightMessage::FIELD_FLIGHT_STEP, $step);
        $message->set(FlightMessage::FIELD_INDEX, date("YmdHis"));
        $message->set(FlightMessage::FIELD_MASKAPAI, $this->request->airline);
        $message->set(FlightMessage::FIELD_RUTE, 0);

        $departureDate = DateTime::createFromFormat('Y-m-d', $this->request->departureDate);

        $message->set(FlightMessage::FIELD_CITY_ORIGIN, $this->request->departure);
        $message->set(FlightMessage::FIELD_CITY_DESTINATION, $this->request->arrival);
        $message->set(FlightMessage::FIELD_DATE_DEPARTURE, $departureDate->format('m/d/Y'));
        $message->set(FlightMessage::FIELD_DATE_ARRIVAL, $departureDate->format('m/d/Y'));
        $message->set(FlightMessage::FIELD_COUNT_ADULT, $this->request->adult);
        $message->set(FlightMessage::FIELD_COUNT_CHILD, $this->request->child);
        $message->set(FlightMessage::FIELD_COUNT_BABY, $this->request->infant);

        $message->set(FlightMessage::FIELD_PROCESS, "01");
        $message->set(FlightMessage::FIELD_ID_PEL1, "TPSW"); // tambahan biar tidak general error
        $this->sendToCore($message);

        SearchResponseParser::instance()->parse($message)->into($this);
    }
}