<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\GetDestinationResponseParser;
use Travel\Libraries\MTI;

class GetDestinationController extends APIController
{
    protected $invoking = "Get Destination Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 1);

        $this->sendToCore($message);

        GetDestinationResponseParser::instance()->parse($message)->into($this);
    }
}