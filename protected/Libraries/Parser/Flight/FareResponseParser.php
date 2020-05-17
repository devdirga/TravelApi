<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class FareResponseParser extends BaseResponseParser implements ResponseParser
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
            $payment_code = $this->message->get(FlightMessage::FIELD_PAYMENT_CODE);

            $data_paycode = json_decode($payment_code);
            $details = explode("#", $this->message->get(FlightMessage::FIELD_MESSAGE));

            $apiController->response->setDataAsObject();

            $apiController->response->data->departureTime = $details[0];
            $apiController->response->data->arrivalTime = strval($details[1]);
            $apiController->response->data->price = strval(intval($details[4]));

            if (!isset($data_paycode->check_in_baggage) || $data_paycode->check_in_baggage == "") {
                $apiController->response->data->baggage = "20";
            } else {
                $apiController->response->data->baggage = $data_paycode->check_in_baggage;
            }

            $apiController->response->data->settings = array();

            $settings = explode("|", $rd);

            foreach ($settings as $setting) {
                $exploded = explode("=", $setting);

                $object = new \stdClass();

                $object->isActive = $exploded[1];
                $object->customAdmin = $exploded[2];
                $object->airlineName = $exploded[3];
                $object->isCaptcha = $exploded[4];
                $object->isInfant = $exploded[5];
                $object->isChild = $exploded[6];
                $object->icon = $exploded[7];
                $object->switcherId = $exploded[8];

                $apiController->response->data->settings[$exploded[0]] = $object;
            }
        }

        $apiController->response->mid = $this->message->get(FlightMessage::FIELD_MID);
        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : $rd);
    }
}