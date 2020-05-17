<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\AccountResponseParser;

class AccountController extends APIController
{
    protected $invoking = "Account App";

    public function indexAction()
    {
        $message = new AppMessage($this);

        $this->sendToCore($message);

        AccountResponseParser::instance()->parse($message)->into($this);
    }
}