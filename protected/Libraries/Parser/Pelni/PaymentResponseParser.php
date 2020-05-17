<?php

namespace Travel\Libraries\Parser\Pelni;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\APIController;
use Travel\Libraries\Utility;

class PaymentResponseParser extends BaseResponseParser implements ResponseParser
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
        $apiController->response->setDataAsObject();

        if (
            Utility::isTesterOutlet($this->message->get(PelniMessage::FIELD_LOKET_ID)) ||
            (isset($apiController->request->simulateSuccess) && $apiController->request->simulateSuccess == true)
        ) {
            $apiController->response->setStatus("00", "Simulate Succes");
            $apiController->response->data->transaction_id =  $this->message->get(PelniMessage::FIELD_TRX_ID);
            $apiController->response->data->url_etiket = "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $this->message->get(PelniMessage::FIELD_TRX_ID);
            $apiController->response->data->url_struk = "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $this->message->get(PelniMessage::FIELD_TRX_ID);
            $apiController->response->data->komisi = Utility::getCommission($apiController, $this->message->get(PelniMessage::FIELD_TRX_ID));
            $apiController->response->data->bookCode = "ABCDEF";
        } else {
            if ($rc == "00") {
                $apiController->response->setStatus("00", "Pembayaran Berhasil");
                $apiController->response->data->transaction_id =  $this->message->get(PelniMessage::FIELD_TRX_ID);
                $apiController->response->data->komisi = Utility::getCommission($apiController, $this->message->get(PelniMessage::FIELD_TRX_ID));
                $apiController->response->data->url_etiket = "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $this->message->get(PelniMessage::FIELD_TRX_ID);
                $apiController->response->data->url_struk = "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $this->message->get(PelniMessage::FIELD_TRX_ID);
                $apiController->response->data->bookCode = $this->message->get(PelniMessage::FIELD_BOOKING_CODE);
            } else {
                $apiController->response->setStatus($rc, $rc == "00" ? "Pembayaran Berhasil" : $rd);
            }
        }
    }
}