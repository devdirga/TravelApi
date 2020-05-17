<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Parser\App\GlobalPaymentResponseParser;
use Travel\Libraries\Message\AppMessage;

class GlobalPaymentController extends APIController
{

    protected $invoking = "Global Payment App";

    public function indexAction()
    {

        $message = new AppMessage($this);

        GlobalPaymentResponseParser::instance()->parse($message)->into($this);
    }
}