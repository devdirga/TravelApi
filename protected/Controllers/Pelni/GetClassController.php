<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\GetClassResponseParser;
use Travel\Libraries\MTI;

class GetClassController extends APIController
{
    protected $invoking = "Get Class Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 4);

        $this->sendToCore($message);

        GetClassResponseParser::instance()->parse($message)->into($this);
    }
}