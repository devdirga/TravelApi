<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\FeeResponseParser;
use Travel\Libraries\MTI;

class FeeController extends APIController
{
    protected $invoking = "Fee Flight";

    public function indexAction()
    {
        $this->setMTI(MTI::SETFEE);
        $this->setProductCode(MTI::SETFEE);

        $message = new FlightMessage($this);

        $feesRequest = (array) $this->request->fees;

        $fees = array();

        foreach ($feesRequest as $key => $value) {
            $fees[] = $key . ":" . $value;
        }

        $message->set(FlightMessage::FIELD_BALANCE, implode("#", $fees));
        $message->set(FlightMessage::FIELD_MASKAPAI, "TPSW");
        $message->set(FlightMessage::FIELD_VIA, "WEB");

        $this->sendToCore($message);

        FeeResponseParser::instance()->parse($message)->into($this);
    }
}