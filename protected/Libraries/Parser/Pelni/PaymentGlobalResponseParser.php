<?php

namespace Travel\Libraries\Parser\Pelni;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\APIController;

class PaymentGlobalResponseParser extends BaseResponseParser implements ResponseParser
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
            $apiController->response->setDataAsObject();

            $apiController->response->data->bookingCode = $this->message->get(PelniMessage::FIELD_BOOKING_CODE);
        }

        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : $rd);
    }
}