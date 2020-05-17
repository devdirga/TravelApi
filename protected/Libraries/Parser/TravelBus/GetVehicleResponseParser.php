<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Travel\Libraries\Parser\TravelBus;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;

/**
 * Description of GetVehicleResponseParser
 *
 * @author bimasakti
 */
class GetVehicleResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * TravelBus message response from core.
     * 
     * @var TravelBusMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(TravelBusMessage::FIELD_STATUS);
        $rd = $this->message->get(TravelBusMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $apiController->response->setDataAsArray();
            $apiController->response->data = json_decode($this->message->get(TravelBusMessage::FIELD_MESSAGE));
        }

        $apiController->response->setStatus($rc, $rd);
    }
}