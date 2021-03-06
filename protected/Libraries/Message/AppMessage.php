<?php

namespace Travel\Libraries\Message;

use Travel\Libraries\APIController;

class AppMessage extends BaseMessage
{
    const FIELD_LOKET_ID = 6;
    const FIELD_PIN = 7;
    const FIELD_TOKEN = 8;
    const FIELD_BALANCE = 9;
    const FIELD_TRX_ID = 10;
    const FIELD_STATUS = 11;
    const FIELD_KETERANGAN = 12;
    const FIELD_MESSAGE_BROADCAST = 13;
    const FIELD_SETTING = 14;

    protected $dataLength = 15;

    public function __construct(APIController $controller)
    {
        parent::__construct($controller);

        $this->fillDefault(6);
    }
}