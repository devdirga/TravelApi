<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class BaggageResponseParser extends BaseResponseParser implements ResponseParser
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
            $details = explode("#", $this->message->get(FlightMessage::FIELD_MESSAGE));
            $keys = explode(";", $details[0]);
            $prices = explode(";", $details[1]);
            $weights = explode(";", $details[2]);

            $apiController->response->rc = "00";
            $apiController->response->rd = "success";
            for ($i = 0; $i < count($keys); $i++) {
                $apiController->response->data[$i] = new \stdClass();
                $apiController->response->data[$i]->baggage_key = $keys[$i];
                $apiController->response->data[$i]->price = $prices[$i];
                $apiController->response->data[$i]->weight = $weights[$i];
            }
        } else {

            $apiController->response->rc = "01";
            $apiController->response->rd = "gagal mendapatkan response";
            $apiController->response->data = "";
        }

        $apiController->response->mid = $this->message->get(FlightMessage::FIELD_MID);
        $apiController->response->setStatus($rc, $rd);
    }
}