<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class FeeResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Flight Message.
     * 
     * @var FlightMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(FlightMessage::FIELD_MASKAPAI);

        if ($rc == "00") {
            $message = $this->message->get(FlightMessage::FIELD_RUTE);

            $apiController->response->setDataAsObject();

            $apiController->response->data->message = $message;
        }

        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : "Gagal, nilai max setting fee Rp. 100.000.");
    }
}