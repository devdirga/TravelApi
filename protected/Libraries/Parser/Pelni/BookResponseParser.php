<?php

namespace Travel\Libraries\Parser\Pelni;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\APIController;
use Travel\Libraries\Utility;
use DateTime;

class BookResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Pelni message response from core.
     * 
     * @var PelniMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(PelniMessage::FIELD_STATUS);
        $rd = $this->message->get(PelniMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $apiController->response->setDataAsObject();

            $arrivalDate = DateTime::createFromFormat("Ymd", $this->message->get(PelniMessage::FIELD_ARRIVAL_DATE));
            $departureTime = DateTime::createFromFormat("Hi", $this->message->get(PelniMessage::FIELD_DEPARTURE_TIME));
            $arrivalTime = DateTime::createFromFormat("Hi", $this->message->get(PelniMessage::FIELD_ARRIVAL_TIME));

            $payLimit = DateTime::createFromFormat("YmdHis", $this->message->get(PelniMessage::FIELD_PAY_LIMIT));

            //$apiController->response->data->nominal = $this->message->get(PelniMessage::FIELD_NOMINAL);
            $apiController->response->data->nominal_admin = $this->message->get(PelniMessage::FIELD_NOMINAL_ADMIN);
            $apiController->response->data->bookingCode = $this->message->get(PelniMessage::FIELD_BOOKING_CODE);
            $apiController->response->data->paymentCode = $this->message->get(PelniMessage::FIELD_PAYMENT_CODE);
            $apiController->response->data->departureTime = $departureTime->format("H:i");
            $apiController->response->data->arrivalDate = $arrivalDate->format("Y-m-d");
            $apiController->response->data->arrivalTime = $arrivalTime->format("H:i");
            $apiController->response->data->seats = json_decode($this->message->get(PelniMessage::FIELD_SEAT));
            $apiController->response->data->expiredAt = $this->message->get(PelniMessage::FIELD_BOOKING_EXPIRED_DATE);
            $apiController->response->data->normalSales = $this->message->get(PelniMessage::FIELD_NORMAL_SALES);
            $apiController->response->data->extraFee = $this->message->get(PelniMessage::FIELD_EXTRA_FEE);
            //$apiController->response->data->commission = $this->message->get(PelniMessage::FIELD_COMMISSION);
            $apiController->response->data->bookingBalance = $this->message->get(PelniMessage::FIELD_BOOK_BALANCE);
            $apiController->response->data->discount = $this->message->get(PelniMessage::FIELD_DISCOUNT);
            $apiController->response->data->payLimit = $payLimit->format("Y-m-d H:i:s");
            $apiController->response->data->komisi = Utility::getKomisi($apiController, $this->message->get(PelniMessage::FIELD_TRX_ID));
            $apiController->response->data->transactionId = $this->message->get(PelniMessage::FIELD_TRX_ID);
        }
        $rd = $this->filterKata($rd);
        $apiController->response->mid = $this->message->get(PelniMessage::FIELD_MID);
        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : $rd);
        error_log("BOOK_PELNI : " . json_encode($apiController->response));
    }
    public function filterKata($var)
    {
        if (strpos($var, 'Each adult can') !== FALSE) {
            $var = "1 penumpang bayi wajib di dampingi oleh 1 penumpang dewasa";
        } elseif (strpos($var, 'A booking must have at least') !== FALSE) {
            $var = "Minimal pemesanan yaitu 1 penumpang Dewasa atau 1 Penumpang anak";
        }
        return $var;
    }
}