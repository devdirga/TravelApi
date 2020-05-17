<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class TicketStatusResponseParser extends BaseResponseParser implements ResponseParser
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

        $message = trim($this->message->get(FlightMessage::FIELD_MESSAGE));

        if ($rc == "00" && $message != "") {
            $transactions = explode("|", $message);

            foreach ($transactions as $transaction) {
                if (trim($transaction) != "") {
                    $apiController->response->data[] = $transaction;
                }
            }
        }

        $apiController->response->setStatus($rc, $rd);
    }
}