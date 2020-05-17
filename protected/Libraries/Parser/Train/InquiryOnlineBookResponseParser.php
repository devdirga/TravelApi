<?php

namespace Travel\Libraries\Parser\Train;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\APIController;
use DateTime;

class InquiryOnlineBookResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Train message response from core.
     * 
     * @var TrainMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(TrainMessage::FIELD_ERR_CODE);
        $rd = $this->message->get(TrainMessage::FIELD_ERR_MSG);

        if ($rc == "00") {
            $trains = json_decode(str_replace(" -,", " \"-\",", $this->message->get(TrainMessage::FIELD_SCHEDULE)));

            foreach ($trains as $train) {
                $object = new \stdClass();

                $departureDate = DateTime::createFromFormat("Ymd", $train[2]);
                $arrivalDate = DateTime::createFromFormat("Ymd", $train[3]);

                $departureTime = DateTime::createFromFormat("Hi", $train[4]);
                $arrivalTime = DateTime::createFromFormat("Hi", $train[5]);

                $object->trainNumber = $train[0];
                $object->trainName = $train[1];
                $object->departureDate = $departureDate->format('Y-m-d');
                $object->arrivalDate = $arrivalDate->format('Y-m-d');
                $object->departureTime = $departureTime->format("H:i");
                $object->arrivalTime = $arrivalTime->format("H:i");
                $object->seats = array();

                $apiController->response->data[] = $object;
            }
        }

        $apiController->response->setStatus($rc == "0" ? "00" : $rc, $rc == "00" ? "Success" : $rd);
    }
}