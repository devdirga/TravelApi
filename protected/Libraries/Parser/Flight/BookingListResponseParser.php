<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class BookingListResponseParser extends BaseResponseParser implements ResponseParser
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

        if ($rc == "00") {
            $transactions = explode("|", $this->message->get(FlightMessage::FIELD_MESSAGE));

            foreach ($transactions as $transaction) {
                $apiController->response->data[] = $transaction;
            }
        }

        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : "Failed");
    }
}