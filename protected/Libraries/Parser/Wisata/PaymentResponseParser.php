<?php

namespace Travel\Libraries\Parser\Wisata;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\WisataMessage;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Utility;

class PaymentResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * TravelBus message response from core.
     * 
     * @var WisataMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(WisataMessage::FIELD_STATUS);
        $rd = $this->message->get(WisataMessage::FIELD_KETERANGAN);
        $apiController->response->setDataAsObject();
        if (
            Utility::isTesterOutlet($this->message->get(WisataMessage::FIELD_LOKET_ID)) ||
            (isset($apiController->request->simulateSuccess) && $apiController->request->simulateSuccess == true)
        ) {
            $apiController->response->setStatus("00", "Simulate Succes");
            $apiController->response->data->transaction_id =  $this->message->get(WisataMessage::FIELD_TRX_ID);
            $apiController->response->data->url_etiket = "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $this->message->get(WisataMessage::FIELD_TRX_ID);
            $apiController->response->data->url_struk = "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $this->message->get(WisataMessage::FIELD_TRX_ID);
            $apiController->response->data->komisi = Utility::getCommission($apiController, $this->message->get(WisataMessage::FIELD_TRX_ID));
            $apiController->response->data->idTransaksi = $this->message->get(WisataMessage::FIELD_TRX_ID);
        } else {
            if ($rc == "00") {
                //$iteneraryQuery = $apiController->db->query("select nama_itinerary as title,detail_itinerary as content from paket_wisata_2_data_itinerary where id_destinasi=? order by urutan asc", [$apiController->request->idDestinasi]);
                $apiController->response->data->idTransaksi = $this->message->get(WisataMessage::FIELD_TRX_ID);
                $apiController->response->data->bookCode = $this->message->get(WisataMessage::FIELD_BOOK_CODE);
                $apiController->response->data->paymentCode = $this->message->get(WisataMessage::FIELD_PAYMENT_CODE);
                $apiController->response->data->nominal = $this->message->get(WisataMessage::FIELD_NOMINAL);
                $apiController->response->data->nominalAdmin = $this->message->get(WisataMessage::FIELD_NOMINAL_ADMIN);
                $apiController->response->data->komisi = Utility::getCommission($apiController, $this->message->get(WisataMessage::FIELD_TRX_ID));
                $apiController->response->data->url_etiket = "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $this->message->get(WisataMessage::FIELD_TRX_ID);
                $apiController->response->data->url_struk = "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $this->message->get(WisataMessage::FIELD_TRX_ID);
            } else {
                $apiController->response->setStatus($rc, $rd);
            }
        }
    }
}