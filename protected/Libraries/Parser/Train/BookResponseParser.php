<?php

namespace Travel\Libraries\Parser\Train;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\Utility;
use Travel\Libraries\APIController;

class BookResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Train message response from core.
     * 
     * @var TrainMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(TrainMessage::FIELD_STATUS);
        $rd = $this->message->get(TrainMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $apiController->response->setDataAsObject();

            $apiController->response->data->bookingCode = $this->message->get(TrainMessage::FIELD_BOOK_CODE);
            $apiController->response->data->transactionId = $this->message->get(TrainMessage::FIELD_TRX_ID);
            $apiController->response->data->passengers = json_decode($this->message->get(TrainMessage::FIELD_PAX));
            $apiController->response->data->seats = json_decode($this->message->get(TrainMessage::FIELD_SEAT));
            $apiController->response->data->komisi = Utility::getKomisi($apiController, $this->message->get(TrainMessage::FIELD_TRX_ID));
            $apiController->response->data->normalSales = $this->message->get(TrainMessage::FIELD_NORMAL_SALES);
            $apiController->response->data->extraFee = $this->message->get(TrainMessage::FIELD_EXTRA_FEE);
            $apiController->response->data->nominalAdmin = $this->message->get(TrainMessage::FIELD_NOMINAL_ADMIN);
            $apiController->response->data->bookBalance = $this->message->get(TrainMessage::FIELD_BOOK_BALANCE);
            $apiController->response->data->discount = intval($this->message->get(TrainMessage::FIELD_SEAT_MAP_NULL));
            $apiController->response->data->timeLimit = $this->getTimeLimit();
        }

        $apiController->response->mid = $this->message->get(TrainMessage::FIELD_MID);
        $apiController->response->setStatus($rc == "0" ? "00" : $rc, $rc == "00" ? "Success" : $rd);
    }

    public function getTimeLimit()
    {
        /* @ 2 jam setelah Booking */

        $timestamp = strtotime(date('Y-m-d H:i:s')) + 60 * 45;
        $time = date('Y-m-d H:i:s', $timestamp);

        return $time;
    }
}