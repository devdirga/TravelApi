<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\PaymentGlobalResponseParser;

class PaymentGlobalController extends APIController
{
    protected $invoking = "Payment Global Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::BAYAR);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 15);

        $message->set(PelniMessage::FIELD_PAYMENT_TYPE, "TUNAI");
        $message->set(PelniMessage::FIELD_PAYMENT_CODE, $this->request->paymentCode);

        $this->sendToCore($message);

        PaymentGlobalResponseParser::instance()->parse($message)->into($this);
    }
}