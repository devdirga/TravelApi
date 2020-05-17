<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\CheckAvailabilityResponseParser;
use Travel\Libraries\MTI;

class CheckAvailabilityController extends APIController
{
    protected $invoking = "Check Availability Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 7);
        $message->set(PelniMessage::FIELD_ORIGINATION, $this->request->origin);
        $message->set(PelniMessage::FIELD_ORIGINATION_CALL, $this->request->originCall);
        $message->set(PelniMessage::FIELD_DESTINATION, $this->request->destination);
        $message->set(PelniMessage::FIELD_DESTINATION_CALL, $this->request->destinationCall);

        $message->set(PelniMessage::FIELD_DEPARTURE_DATE, str_replace("-", "", $this->request->departureDate));
        $message->set(PelniMessage::FIELD_SHIP_NUMBER, $this->request->shipNumber);

        $message->set(PelniMessage::FIELD_SUB_CLASS, $this->request->subClass);
        $message->set(PelniMessage::FIELD_MALE_PAX, $this->request->male);
        $message->set(PelniMessage::FIELD_FEMALE_PAX, $this->request->female);

        $this->sendToCore($message);

        CheckAvailabilityResponseParser::instance()->parse($message)->into($this);
    }
}