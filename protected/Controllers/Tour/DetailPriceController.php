<?php

namespace Travel\Tour;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TourMessage;
use Travel\Libraries\Parser\Tour\DetailPriceResponseParser;

class DetailPriceController extends APIController
{
    protected $invoking = "Detail Price Tour";

    public function indexAction()
    {
        $message = new TourMessage($this);

        $this->sendToCore($message);

        DetailPriceResponseParser::instance()->parse($message)->into($this);
    }
}