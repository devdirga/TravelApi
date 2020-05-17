<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\GetOriginDestinationResponseParser;
use Travel\Libraries\MTI;

class GetOriginDestinationController extends APIController
{
    protected $invoking = "Get Origin Destination Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 2);

        $this->sendToCore($message);

        GetOriginDestinationResponseParser::instance()->parse($message)->into($this);
    }
}