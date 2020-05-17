<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\FareResponseParser;
use Travel\Libraries\MTI;

class FareController extends APIController
{
    protected $invoking = "Fare Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 6);
        $message->set(PelniMessage::FIELD_ORIGINATION, $this->request->origin);
        $message->set(PelniMessage::FIELD_ORIGINATION_CALL, $this->request->originCall);
        $message->set(PelniMessage::FIELD_DESTINATION, $this->request->destination);
        $message->set(PelniMessage::FIELD_DESTINATION_CALL, $this->request->destinationCall);

        $message->set(PelniMessage::FIELD_DEPARTURE_DATE, str_replace("-", "", $this->request->departureDate));
        $message->set(PelniMessage::FIELD_SHIP_NUMBER, $this->request->shipNumber);

        $this->sendToCore($message);

        FareResponseParser::instance()->parse($message)->into($this);
    }
}