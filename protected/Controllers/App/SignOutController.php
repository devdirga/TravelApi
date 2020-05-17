<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\SignOutResponseParser;

class SignOutController extends APIController
{
    protected $invoking = "Sign Out App";

    public function indexAction()
    {
        $this->setMTI("SIGNOFF");
        $this->setProductCode("SIGNOFF");

        $message = new AppMessage($this);

        $message->set(AppMessage::FIELD_LOKET_ID, $this->getOutletId());
        $message->set(AppMessage::FIELD_PIN, $this->getPin());
        $message->set(AppMessage::FIELD_TOKEN, $this->getKey());

        $this->sendToCore($message);

        SignOutResponseParser::instance()->parse($message)->into($this);
    }
}