<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\HolidayResponseParser;

class HolidayController extends APIController
{
    protected $invoking = "Holiday App";

    public function indexAction()
    {
        $message = new AppMessage($this);

        HolidayResponseParser::instance()->parse($message)->into($this);
    }
}