<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\ProductCode;
use Travel\Libraries\Message\DepMessage;
use Travel\Libraries\HttpConnect;
use Phalcon\Db;

/**
 * Description of ListDepositController
 *
 * @author bimasakti
 */

class ListDepositController extends APIController
{
    protected $invoking = "List Deposit App";

    public function indexAction()
    {
        if ($this->request->is24) {

            foreach ($this->db->fetchAll("SELECT bank,no_va FROM mt_outlet_va WHERE id_outlet = ?", Db::FETCH_OBJ, [$this->getOutletId()]) as $value) {
                if ($value->bank === explode("-", ProductCode::BNI24)[0]) {
                    $vaBni = $value->no_va;
                } else if ($value->bank === explode("-", ProductCode::BRI24)[0]) {
                    $vaBri = $value->no_va;
                }
            }

            //BNI
            if (empty($vaBni)) {
                $messageBni = new DepMessage($this);
                $messageBni->set(DepMessage::FIELD_MTI, ProductCode::TOPUP);
                $messageBni->set(DepMessage::FIELD_KODE_PRODUK, ProductCode::TOPUP);
                $messageBni->set(DepMessage::FIELD_BANK, explode("-", ProductCode::BNI24)[0]);
                $messageBni->set(DepMessage::FIELD_NOMINAL, "20000");
                $messageBni->set(DepMessage::FIELD_VIA, 'MOBILE_SMART');
                $messageBni->set(DepMessage::FIELD_DATETIME, date("Ymdhis"));
                $messageBni->set(DepMessage::FIELD_STEP, '1');
                $messageBni->set(DepMessage::FIELD_LOKET_ID, $this->getOutletId());
                $messageBni->set(DepMessage::FIELD_PIN, $this->getPin());
                $messageBni->set(DepMessage::FIELD_TOKEN, 'travel');
                $responseBni = HttpConnect::sendBank24('viadesktop=' . $messageBni->toString(), ProductCode::BNI24);
                $bniDescription = self::parseMessage($responseBni);
            } else {
                $bniDescription = "No Rekening VA Anda " . $vaBni . " Atas nama " . $this->getOutletId() . " . Pilih Transaksi Lain > Pembayaran > Lainnya > BNIVA . Masukkan nomor rekening deposit BNI Anda . Pastikan data di layar sesuai dengan nama Deposit Travel (" . $this->getOutletId() . "). Biaya admin BNI VA Rp 2.500,-";
            }

            //BRI
            if (empty($vaBri)) {
                $messageBri = new DepMessage($this);
                $messageBri->set(DepMessage::FIELD_MTI, ProductCode::TOPUP);
                $messageBri->set(DepMessage::FIELD_KODE_PRODUK, ProductCode::TOPUP);
                $messageBri->set(DepMessage::FIELD_BANK, explode("-", ProductCode::BRI24)[0]);
                $messageBri->set(DepMessage::FIELD_NOMINAL, "20000");
                $messageBri->set(DepMessage::FIELD_VIA, 'MOBILE_SMART');
                $messageBri->set(DepMessage::FIELD_DATETIME, date("Ymdhis"));
                $messageBri->set(DepMessage::FIELD_STEP, '1');
                $messageBri->set(DepMessage::FIELD_LOKET_ID, $this->getOutletId());
                $messageBri->set(DepMessage::FIELD_PIN, $this->getPin());
                $messageBri->set(DepMessage::FIELD_TOKEN, 'travel');
                $responseBri = HttpConnect::sendBank24('viadesktop=' . $messageBri->toString(), ProductCode::BRI24);
                $briDescription = self::parseMessage($responseBri);
            } else {
                $briDescription = "No Rekening VA Anda " . $vaBri . " Atas nama " . $this->getOutletId() . " . Pilih Transaksi Lain > Pembayaran > Lainnya > BRIVA . Masukkan nomor rekening deposit BNI Anda . Pastikan data di layar sesuai dengan nama Deposit Travel (" . $this->getOutletId() . "). Biaya admin BNI VA Rp 2.500,-";
            }

            $description = "Anda dapat melakukan isi ulang saldo 24 Jam. Pilih Bank yang anda gunakan untuk transfer. Transfer sesuai dengan nominal yang anda isikan";

            $descriptionAlfa = "Untuk melakukan Isi Saldo tanpa permintaan Tiket, silakan infokan ID yang terdaftar di Travel kepada kasir gerai Alfamart, pilih nominal top up yang diinginkan. Berikut nominal top up yang tersedia beserta biaya adminnya:\nRp 50.000,- Rp 2.500,-\nRp 100.000,- Rp 2.500,-\nRp 200.000,- Rp 2.500,-\nRp 300.000,- Rp 2.500,-\nRp 500.000,- Rp 2.500,-\nRp 1.000.000,- Rp 5.000,-\nRp 2.000.000,- Rp 5.000,-\nSelamat Memanfaatkan Kemudahan Top Up Deposit Real Time melalui gerai Alfamart terdekat.";

            $this->response->data = array(
                array('namaBank' => 'BCA-24', "description" => $description),
                array('namaBank' => 'MANDIRI-24', "description" => $description),
                array('namaBank' => 'BNI-24', "description" => $bniDescription),
                array('namaBank' => 'BRI-24', "description" => $briDescription),
                array('namaBank' => 'ALFAMART', "description" => $descriptionAlfa)
            );

            /*
            $this->response->data = array(
                array('namaBank'=>'BCA-24'),
                array('namaBank'=>'MANDIRI-24'),
                array('namaBank'=>'BNI-24'),
                array('namaBank'=>'BRI-24'),
                array('namaBank'=>'ALFAMART'));
             * 
             */
        } else {
            $this->response->data = array(
                array('namaBank' => 'BCA'),
                array('namaBank' => 'MANDIRI'),
                array('namaBank' => 'BNI'),
                array('namaBank' => 'BRI')
            );
        }
        //array('namaBank'=>'ALFAMART')
    }

    public static function parseMessage($response)
    {
        $r = explode("*", urldecode($response));
        $rc = $r[13];
        $rd = $r[14];
        return $rd;
    }
}