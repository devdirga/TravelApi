<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\BookInfoGlobalResponseParser;
use Travel\Libraries\MTI;

class BookInfoGlobalController extends APIController
{
    protected $invoking = "Book Info Global Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 9);

        if (isset($this->request->bookingCode)) {
            $message->set(PelniMessage::FIELD_BOOKING_CODE, $this->request->bookingCode);
        }

        $message->set(PelniMessage::FIELD_PAYMENT_CODE, $this->request->paymentCode);

        $this->sendToCore($message);

        BookInfoGlobalResponseParser::instance()->parse($message)->into($this);
    }
}