<?php

namespace Travel\Libraries\Parser\Pelni;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\APIController;
use Phalcon\Db;

class BookInfoResponseParser extends BaseResponseParser implements ResponseParser
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

            $apiController->response->data = json_decode($this->message->get(PelniMessage::FIELD_DATA));
            $trx = $apiController->db->query("select id_transaksi from transaksi where id_produk='SHPPELNI' and bill_info2=? and response_code='00' and jenis_transaksi=0", [$this->message->get(PelniMessage::FIELD_PAYMENT_CODE)]);
            $trx->setFetchMode(Db::FETCH_OBJ);
            $id_transaksi = $trx->fetch()->id_transaksi;
            $apiController->response->transactionId = "" . $id_transaksi;
        }

        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : $rd);
    }
}