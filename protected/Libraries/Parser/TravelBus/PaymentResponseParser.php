<?php

namespace Travel\Libraries\Parser\TravelBus;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;

class PaymentResponseParser extends BaseResponseParser implements ResponseParser
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
            $apiController->response->setDataAsObject();

            $apiController->response->data->message = $this->message->get(TravelBusMessage::FIELD_MESSAGE);
            $apiController->response->data->bookingCode = $this->message->get(TravelBusMessage::FIELD_KODE_BOOKING);
            $apiController->response->data->paymentCode = $this->message->get(TravelBusMessage::FIELD_KODE_PEMBAYARAN);
            $apiController->response->data->tiketNo = $this->message->get(TravelBusMessage::FIELD_NO_TIKET);
            $apiController->response->data->nominal = $this->message->get(TravelBusMessage::FIELD_NOMINAL);
            $apiController->response->data->nominalAdmin = $this->message->get(TravelBusMessage::FIELD_NOMINAL_ADMIN);
        }

        $apiController->response->setStatus($rc, $rd);
    }
}