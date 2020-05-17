<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\DataPaxResponseParser;

class DataPaxController extends APIController
{
    protected $invoking = "History Pax App";

    public function indexAction()
    {
        $message = new AppMessage($this);

        DataPaxResponseParser::instance()->parse($message)->into($this);
    }
}