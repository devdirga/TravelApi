<?php

namespace Travel\Pelni;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\Parser\Pelni\PaymentResponseParser;
use Travel\Libraries\Utility;
use Travel\Libraries\MTI;
use Phalcon\Db;

class PaymentController extends APIController
{
    protected $invoking = "Payment Pelni";

    public function indexAction()
    {
        $this->setMTI(MTI::BAYAR);
        $this->setProductCode("SHPPELNI");

        $message = new PelniMessage($this);

        $message->set(PelniMessage::FIELD_OPERATION, 14);

        $message->set(PelniMessage::FIELD_PAYMENT_TYPE, "TUNAI");
        $message->set(PelniMessage::FIELD_PAYMENT_CODE, $this->request->paymentCode);
        $message->set(PelniMessage::FIELD_TRX_ID, $this->request->transactionId);

        $this->request->simulateSuccess = $this->config->environment;

        if (!$this->request->simulateSuccess) {
            /* Check if transaction exist */

            $data = $this->db->fetchOne("SELECT bill_info4,bill_info9,bill_info11,bill_info24,bill_info25 FROM transaksi WHERE bill_info2 = ?", Db::FETCH_OBJ, [$this->request->paymentCode]);

            $ifExistPayment = $this->db->fetchOne("SELECT id_transaksi FROM transaksi WHERE bill_info4 = ? AND bill_info9 = ? AND bill_info11 = ? AND bill_info24 = ? AND bill_info25 = ? AND jenis_transaksi = 1 AND response_code = ?", Db::FETCH_OBJ, [$data->bill_info4, $data->bill_info9, $data->bill_info11, $data->bill_info24, $data->bill_info25, '00']);

            if ($ifExistPayment) {
                $message->set(PelniMessage::FIELD_STATUS, '33');
                $message->set(PelniMessage::FIELD_KETERANGAN, 'Pembayaran untuk transaksi sebelumnya telah sukses, e-ticket dapat dilihat di halaman menu pesnan saya');
            } else {

                if (!Utility::isTesterOutlet($this->getOutletId())) {

                    $this->sendToCore($message);
                } else {
                    $message->parse('BAYAR*SHPPELNI*3625788300*7*20190729225628*MOBILE*****10000*FA153627*------*------*806429*1**171*1458499493*00*SUKSES*14***********************************TUNAI*TWNYKB*8822249044*********0***');
                }
            }
        }

        PaymentResponseParser::instance()->parse($message)->into($this);
    }
}