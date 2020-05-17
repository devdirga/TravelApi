<?php

namespace Travel\Libraries\Parser\Train;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\APIController;

class PaymentOnlineBookResponseParser extends BaseResponseParser implements ResponseParser
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

        //$apiController->response->setStatus($rc == "0" ? "00" : $rc, $rc == "00" ? "Success" : $rd);
        if (isset($apiController->request->simulateSuccess) && $apiController->request->simulateSuccess == true) {
            $apiController->response->setStatus("00", "Sukses.");
        } else if (isset($apiController->request->simulateSuccess) && $apiController->request->simulateSuccess == false) {
            $apiController->response->setStatus("01", "Gagal.");
        }
    }
}