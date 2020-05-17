<?php

namespace Travel\Libraries\Parser\Train;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\APIController;

class GetSeatLayoutResponseParser extends BaseResponseParser implements ResponseParser
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
            $wagons = json_decode($this->message->get(TrainMessage::FIELD_SEAT_MAP_NULL));

            foreach ($wagons as $wagon) {
                $object = new \stdClass();

                $object->wagonCode = $wagon[0];
                $object->wagonNumber = $wagon[1];
                $object->layout = array();

                $this->iterateSeats($object->layout, $wagon[2]);

                $apiController->response->data[] = $object;
            }
        }

        $apiController->response->setStatus($rc == "0" ? "00" : $rc, $rc == "00" ? "Success" : $rd);
    }

    private function iterateSeats(&$layout, $seats)
    {
        foreach ($seats as $seat) {
            $object = new \stdClass();

            $object->row = $seat[0];
            $object->column = $seat[1];
            $object->class = $seat[2];
            $object->isFilled = $seat[3];

            $layout[] = $object;
        }
    }
}