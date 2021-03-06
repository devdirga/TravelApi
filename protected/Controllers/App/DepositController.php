<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\ProductCode;
use Travel\Libraries\Message\DepMessage;
use Travel\Libraries\HttpConnect;
use Travel\Libraries\Models\Outlet;

class DepositController extends APIController
{

    protected $invoking = "Deposit App";

    protected $BANK24 = array('MANDIRI-24', 'BCA-24', 'BNI-24', 'BRI-24');

    //ProductCode::BANK24

    public function indexAction()
    {

        $message = new DepMessage($this);

        $message->set(DepMessage::FIELD_MTI, (in_array($this->request->bank, $this->BANK24)) ? ProductCode::TOPUP : ProductCode::TIKET);
        $message->set(DepMessage::FIELD_KODE_PRODUK, (in_array($this->request->bank, $this->BANK24)) ? ProductCode::TOPUP : ProductCode::TIKET);
        $message->set(DepMessage::FIELD_BANK, (in_array($this->request->bank, $this->BANK24)) ? explode('-', $this->request->bank)[0] : $this->request->bank);
        $message->set(DepMessage::FIELD_NOMINAL, $this->request->nominal);
        $message->set(DepMessage::FIELD_VIA, 'MOBILE_SMART');
        $message->set(DepMessage::FIELD_DATETIME, date("Ymdhis"));
        $message->set(DepMessage::FIELD_STEP, '1');
        $message->set(DepMessage::FIELD_LOKET_ID, $this->getOutletId());
        $message->set(DepMessage::FIELD_PIN, $this->getPin());
        $message->set(DepMessage::FIELD_TOKEN, 'travel');

        $this->response = $this->parseMessage(HttpConnect::sendBank24((in_array($this->request->bank, $this->BANK24)) ? 'viadesktop=' . $message->toString() : $message->toString(), $this->request->bank), Outlet::take($this->getOutletId(), 'notelpPemilik'), $this->request->bank);
    }

    public function parseMessage($response, $phone, $bank)
    {

        $d = explode('*', urldecode($response));

        $rc = $d[13];

        $rd = $d[14];

        switch ($bank) {
            case ProductCode::MANDIRI24:
                break;
            case ProductCode::BCA24:
                $rd = str_replace('#', '. ', $rd);
                break;
            case ProductCode::BNI24:
            case ProductCode::BRI24;
                $rd = explode('Tata cara topup deposit', str_replace('#', '. ', $rd))[0];
                break;
            case ProductCode::ALFAMART;
            case ProductCode::ALFAMART24;
                $rc = "00";
                $rd = "Untuk melakukan Isi Saldo tanpa permintaan Tiket, silakan infokan ID FT yang terdaftar di Travel kepada kasir gerai Alfamart,pilihan nominal deposit 50 ribu,100 ribu,200 ribu,300 ribu,500 ribu,1 juta,2 juta
                       *Deposit Rp 50.000 s/d 500.000 biaya admin Rp 2.500
                       *Deposit Rp 1.000.000 s/d 2.000.000 biaya admin Rp 5.000";
                break;
            default:
                break;
        }

        /*
        $rc = '00';
                $rd = 'Untuk melakukan Isi Saldo tanpa permintaan Tiket, silakan infokan ID yang terdaftar di Travel kepada kasir gerai Alfamart, pilih nominal top up yang diinginkan.\nBerikut nominal top up yang tersedia beserta biaya adminnya:\nRp 50.000,- Rp 2.500,-\nRp 100.000,- Rp 2.500,-\nRp 200.000,- Rp 2.500,-\nRp 300.000,- Rp 2.500,-\nRp 500.000,- Rp 2.500,-\nRp 1.000.000,- Rp 5.000,-\nRp 2.000.000,- Rp 5.000,-\nSelamat Memanfaatkan Kemudahan Top Up Deposit Real Time melalui gerai Alfamart terdekat.';
                break;
         *          */

        if ($rc === '00') {
            $rd = str_ireplace('Mitra travel', 'Mitra Travel', $rd);
            $rd .= (in_array($bank, $this->BANK24)) ? '' : '';

            if (strpos($rd, 'Secara Otomatis') !== false) {
                $rd .= ' setelah 10 menit';
            }

            HttpConnect::sendSms(array('nohp' => $phone, 'konten_sms' => $rd, 'cmd' => 'sendsms', 'id_produk' => 'FTDEP'));
            return array('rc' => '00', 'rd' => 'success', 'data' => array('description' => $rd));
        } else if ($rc === '01') {
            $rd = 'Silahkan ulangi kembali, Proses tidak berhasil';
        } else if ($rc === '07') {
            $rd =  'Tiket deposit anda masih aktif ' . $rd;
        }

        //HttpConnect::sendSms(array('nohp' => $phone, 'konten_sms' => $rd, 'cmd' => 'sendsms', 'id_produk' => 'FTDEP'));

        return array('rc' => $rc, 'rd' => $rd);
    }
}