<?php

namespace Travel\Libraries\Parser\TravelBus;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;

class ChooseSeatResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * TravelBus message response from core.
     * 
     * @var TravelBusMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
    }
}