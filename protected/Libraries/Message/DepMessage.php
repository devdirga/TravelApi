<?php

namespace Travel\Libraries\Message;

use Travel\Libraries\APIController;

class DepMessage extends BaseMessage
{
    const FIELD_BANK = 6;
    const FIELD_NOMINAL = 7;
    const FIELD_LOKET_ID = 8;
    const FIELD_PIN = 9;
    const FIELD_TOKEN = 10;

    protected $dataLength = 11;

    public function __construct(APIController $controller)
    {
        parent::__construct($controller);

        $this->fillDefault(6);
    }
}