<?php

namespace Travel\Hotel;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\HotelMessage;
use Travel\Libraries\Parser\Hotel\DestinationResponseParser;

class DestinationController extends APIController
{
    protected $invoking = "Destination Hotel";

    public function indexAction()
    {
        $message = new HotelMessage($this);

        DestinationResponseParser::instance()->parse($message)->into($this);
    }
}