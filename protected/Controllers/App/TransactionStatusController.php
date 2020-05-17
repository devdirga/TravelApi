<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\TransactionStatusResponseParser;

class TransactionStatusController extends APIController
{
    protected $invoking = "Transaction Status App";



    public function indexAction()
    {
        $message = new AppMessage($this);

        $message->set(AppMessage::FIELD_LOKET_ID, $this->getOutletId());
        $message->set(AppMessage::FIELD_PIN, $this->getPin());
        $message->set(AppMessage::FIELD_TOKEN, $this->getKey());

        TransactionStatusResponseParser::instance()->parse($message, $this)->into($this);
    }
}