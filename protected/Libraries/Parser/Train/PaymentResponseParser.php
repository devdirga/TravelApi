<?php

namespace Travel\Libraries\Parser\Train;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\APIController;
use Travel\Libraries\Utility;

class PaymentResponseParser extends BaseResponseParser implements ResponseParser
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
        $apiController->response->setDataAsObject();

        if (
            Utility::isTesterOutlet($this->message->get(TrainMessage::FIELD_LOKET_ID)) ||
            (isset($apiController->request->simulateSuccess) && $apiController->request->simulateSuccess == true)
        ) {
            $apiController->response->setStatus("00", "Simulate Succes");
            $apiController->response->data->transaction_id =  $this->message->get(TrainMessage::FIELD_TRX_ID);
            $apiController->response->data->url_etiket = "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $this->message->get(TrainMessage::FIELD_TRX_ID);
            $apiController->response->data->url_struk = "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $this->message->get(TrainMessage::FIELD_TRX_ID);
            $apiController->response->data->komisi = Utility::getKomisi($apiController, $this->message->get(TrainMessage::FIELD_TRX_ID));
        } else {
            if ($rc == "00") {
                $apiController->response->setStatus("00", "Succes");
                $apiController->response->data->transaction_id =  $this->message->get(TrainMessage::FIELD_TRX_ID);
                $apiController->response->data->url_etiket = "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $this->message->get(TrainMessage::FIELD_TRX_ID);
                $apiController->response->data->url_struk = "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $this->message->get(TrainMessage::FIELD_TRX_ID);
                $apiController->response->data->komisi = Utility::getKomisi($apiController, $this->message->get(TrainMessage::FIELD_TRX_ID));
            } else {
                $apiController->response->setStatus($rc, $rd);
            }
        }

        $apiController->response->mid = $this->message->get(TrainMessage::FIELD_MID);
    }
}