<?php

namespace Travel\Hotel;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\HotelMessage;
use Travel\Libraries\Parser\Hotel\NearbyResponseParser;

class NearbyController extends APIController
{
    protected $invoking = "Hotel Nearby Hotel";

    public function indexAction()
    {
        $message = new HotelMessage($this);

        NearbyResponseParser::instance()->parse($message)->into($this);
    }
}