<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\BookInfoResponseParser;
use Travel\Libraries\MTI;

class BookInfoController extends APIController
{
    protected $invoking = "Book Info Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 8);

        if (isset($this->request->bookingCode)) {
            $message->set(PelniMessage::FIELD_BOOKING_CODE, $this->request->bookingCode);
        }

        $message->set(PelniMessage::FIELD_PAYMENT_CODE, $this->request->paymentCode);

        $this->sendToCore($message);

        BookInfoResponseParser::instance()->parse($message)->into($this);
    }
}