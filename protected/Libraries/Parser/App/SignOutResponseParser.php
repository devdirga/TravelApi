<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;

class SignOutResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * AppMessage.
     * 
     * @var AppMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(AppMessage::FIELD_STATUS);
        $rd = $this->message->get(AppMessage::FIELD_KETERANGAN);

        $apiController->response->setStatus($rc, $rd);
    }
}