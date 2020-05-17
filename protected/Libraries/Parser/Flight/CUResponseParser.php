<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class CUResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Flight Message.
     * 
     * @var FlightMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(FlightMessage::FIELD_STATUS);
        $rd = $this->message->get(FlightMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $apiController->response->setDataAsObject();

            $apiController->response->data->message = $this->message->get(FlightMessage::FIELD_MESSAGE);
        }

        $apiController->response->setStatus($rc, $rd == "" ? "Success" : "");
    }
}