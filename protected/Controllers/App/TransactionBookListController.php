<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\TransactionBookListResponseParser;

class TransactionBookListController extends APIController
{
    protected $invoking = "Transaction List App";

    public function indexAction()
    {

        $message = new AppMessage($this);

        $message->set(AppMessage::FIELD_LOKET_ID, $this->getOutletId());
        $message->set(AppMessage::FIELD_PIN, $this->getPin());
        $message->set(AppMessage::FIELD_TOKEN, $this->getKey());

        //$this->sendToCore($message);

        TransactionBookListResponseParser::instance()->parse($message)->into($this);
    }
}