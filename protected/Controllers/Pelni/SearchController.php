<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\SearchResponseParser;
use Travel\Libraries\MTI;

class SearchController extends APIController
{
    protected $invoking = "Search Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 5);
        $message->set(PelniMessage::FIELD_ORIGINATION, $this->request->origin);
        $message->set(PelniMessage::FIELD_DESTINATION, $this->request->destination);
        $message->set(PelniMessage::FIELD_DEPARTURE_START_DATE, str_replace("-", "", $this->request->startDate));
        $message->set(PelniMessage::FIELD_DEPARTURE_END_DATE, str_replace("-", "", $this->request->endDate));

        $this->sendToCore($message);

        SearchResponseParser::instance()->parse($message)->into($this);
    }
}