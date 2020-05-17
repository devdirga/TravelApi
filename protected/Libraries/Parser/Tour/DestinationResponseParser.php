<?php

namespace Travel\Libraries\Parser\Tour;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TourMessage;
use Travel\Libraries\APIController;

class DestinationResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Tour message response from core.
     * 
     * @var TourMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
    }
}