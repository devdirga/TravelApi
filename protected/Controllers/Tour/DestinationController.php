<?php

namespace Travel\Tour;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TourMessage;
use Travel\Libraries\Parser\Tour\DestinationResponseParser;

class DestinationController extends APIController
{
    protected $invoking = "Destination Tour";

    public function indexAction()
    {
        $message = new TourMessage($this);

        $this->sendToCore($message);

        DestinationResponseParser::instance()->parse($message)->into($this);
    }
}