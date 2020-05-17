<?php

namespace Travel\Tour;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TourMessage;
use Travel\Libraries\Parser\Tour\DetailResponseParser;

class DetailController extends APIController
{
    protected $invoking = "Detail Tour";

    public function indexAction()
    {
        $message = new TourMessage($this);

        $this->sendToCore($message);

        DetailResponseParser::instance()->parse($message)->into($this);
    }
}