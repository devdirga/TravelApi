<?php

namespace Travel\Tour;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TourMessage;
use Travel\Libraries\Parser\Tour\PaymentResponseParser;

class PaymentController extends APIController
{
    protected $invoking = "Payment Tour";

    public function indexAction()
    {
        $message = new TourMessage($this);

        $this->sendToCore($message);

        PaymentResponseParser::instance()->parse($message)->into($this);
    }
}