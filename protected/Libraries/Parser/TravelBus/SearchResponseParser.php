<?php

namespace Travel\Libraries\Parser\TravelBus;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;

class SearchResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * TravelBus message response from core.
     * 
     * @var TravelBusMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(TravelBusMessage::FIELD_STATUS);
        $rd = $this->message->get(TravelBusMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $apiController->response->data = json_decode($this->message->get(TravelBusMessage::FIELD_MESSAGE));
        }

        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : $rd);
    }
}