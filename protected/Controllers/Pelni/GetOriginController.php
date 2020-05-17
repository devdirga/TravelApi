<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\GetOriginResponseParser;
use Travel\Libraries\MTI;

class GetOriginController extends APIController
{
    protected $invoking = "Get Origin Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 0);

        $this->sendToCore($message);

        GetOriginResponseParser::instance()->parse($message)->into($this);
    }
}