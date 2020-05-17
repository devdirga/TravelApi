<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;
use Travel\Libraries\Utility;

class PaymentResponseParser extends BaseResponseParser implements ResponseParser
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
        $apiController->response->setDataAsObject();

        if (
            Utility::isTesterOutlet($this->message->get(FlightMessage::FIELD_LOKET_ID)) ||
            (isset($apiController->request->simulateSuccess) && $apiController->request->simulateSuccess == true)
        ) {
            $apiController->response->setStatus("00", "Simulate Success");
            $apiController->response->data->transaction_id =  $this->message->get(FlightMessage::FIELD_TRX_ID);
            $apiController->response->data->url_etiket = "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $this->message->get(FlightMessage::FIELD_TRX_ID);
            $apiController->response->data->url_struk = "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $this->message->get(FlightMessage::FIELD_TRX_ID);
            $apiController->response->data->komisi = Utility::getKomisi($apiController, $this->message->get(FlightMessage::FIELD_TRX_ID));
            $apiController->response->data->nominal = $this->message->get(FlightMessage::FIELD_NOMINAL);
        } else {
            if ($rc === "00" || $rc === "60") {
                $apiController->response->setStatus("00", ($rc === "00" ? "Pembayaran Berhasil" : "Pembayaran Anda sedang dalam proses, status ISSUE dapat dilihat di menu Pesanan Saya"));
                $apiController->response->data->transaction_id =  $this->message->get(FlightMessage::FIELD_TRX_ID);
                $apiController->response->data->url_etiket = "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $this->message->get(FlightMessage::FIELD_TRX_ID);
                $apiController->response->data->url_struk = "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $this->message->get(FlightMessage::FIELD_TRX_ID);
                $apiController->response->data->komisi = Utility::getKomisi($apiController, $this->message->get(FlightMessage::FIELD_TRX_ID));
                $apiController->response->data->nominal = $this->message->get(FlightMessage::FIELD_NOMINAL);
            } else {
                $apiController->response->setStatus($rc, $rd);
            }
        }

        error_log("FlightPayment : " . json_encode($apiController->response));
    }
}