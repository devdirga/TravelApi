<?php

namespace Travel\Train;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\Parser\Train\StationResponseParser;

class StationController extends APIController
{
    protected $invoking = "Station Train";

    public function indexAction()
    {
        StationResponseParser::instance()->parse(new TrainMessage($this))->into($this);
    }
}