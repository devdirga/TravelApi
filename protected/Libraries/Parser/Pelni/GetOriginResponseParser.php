<?php

namespace Travel\Libraries\Parser\Pelni;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\APIController;

class GetOriginResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Pelni message response from core.
     * 
     * @var PelniMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(PelniMessage::FIELD_STATUS);
        $rd = $this->message->get(PelniMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $data = json_decode($this->message->get(PelniMessage::FIELD_DATA));

            $apiController->response->data = $data->org;
        }

        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : $rd);
    }
}