<?php

namespace Travel\Libraries\Parser\Train;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\APIController;

class ChangeSeatResponseParser extends BaseResponseParser implements ResponseParser
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
            $apiController->response->setDataAsObject();

            $apiController->response->transactionId = $this->message->get(TrainMessage::FIELD_TRX_ID);
            $apiController->response->seats = $apiController->request->seats;
            $apiController->response->bookingCode = $apiController->request->bookingCode;
        }

        $apiController->response->setStatus($rc == "0" ? "00" : $rc, $rc == "00" ? "Success" : $rd);
    }
}