<?php

namespace Travel\Wisata;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\WisataMessage;
use Travel\Libraries\Parser\Wisata\DestinationResponseParser;

class DestinationController extends APIController
{
    protected $invoking = "Destination Wisata";

    public function indexAction()
    {
        $message = new WisataMessage($this);

        DestinationResponseParser::instance()->parse($message)->into($this);
    }
}