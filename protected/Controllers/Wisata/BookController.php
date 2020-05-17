<?php

namespace Travel\Wisata;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\WisataMessage;
use Travel\Libraries\Parser\Wisata\BookResponseParser;
use Travel\Libraries\Utility;
use Travel\Libraries\MTI;

class BookController extends APIController
{
    protected $invoking = "Book Wisata";

    public function indexAction()
    {
        $this->setMTI(MTI::WISATABOOK);
        $this->setProductCode($this->request->produk); //TOURNEW

        $message = new WisataMessage($this);

        $message->set(WisataMessage::FIELD_TOUR_ID, $this->request->idDestinasi);
        $message->set(WisataMessage::FIELD_ID_PEL1, $this->request->hpPemesan);
        $message->set(WisataMessage::FIELD_ID_PEL2, $this->request->namaPemesan);
        $message->set(WisataMessage::FIELD_ID_PEL3, $this->request->email);
        $message->set(WisataMessage::FIELD_PROVINCE, $this->request->propinsi);
        $message->set(WisataMessage::FIELD_DURATION, $this->request->durasi);
        $message->set(WisataMessage::FIELD_TOUR_DURATION, $this->request->durasi);
        $message->set(WisataMessage::FIELD_TOUR_NAME, str_replace("&", "dan", str_replace("\u0026", "dan", $this->request->namaDestinasi)));
        $message->set(WisataMessage::FIELD_HOTEL_ACCOMODATION_NAME, $this->request->rateHotel);
        $message->set(WisataMessage::FIELD_TOUR_START_DATE, self::getDepartureDate($this->request->tanggalMulai));
        //$message->set(WisataMessage::FIELD_TOUR_START_DATE, $this->request->tanggalMulai);
        //$message->set(WisataMessage::FIELD_TOUR_END_DATE, $this->request->tanggalSelesai);
        $message->set(WisataMessage::FIELD_TOUR_END_DATE, self::getArrivalDate(self::getDepartureDate($this->request->tanggalMulai), $this->request->durasi));
        $message->set(WisataMessage::FIELD_TOTAL_PRICE, $this->request->totalHarga);
        $message->set(WisataMessage::FIELD_PAX_PRICE, $this->request->hargaPax);
        $message->set(WisataMessage::FIELD_PAX_NAME, $this->request->namaPeserta);
        $message->set(WisataMessage::FIELD_PAX_PHONE_NUMBER, Utility::normalizePhone($this->request->hpPeserta));
        $message->set(WisataMessage::FIELD_PAX_COUNT, $this->request->jumlahPax);
        $message->set(WisataMessage::FIELD_BUYER_NAME, $this->request->namaPemesan);
        $message->set(WisataMessage::FIELD_BUYER_PHONE_NUMBER, Utility::normalizePhone($this->request->hpPemesan));
        $message->set(WisataMessage::FIELD_INFORMATION_ADDITIONAL, isset($this->request->keterangan) ? $this->request->keterangan : "-");
        if (isset($this->request->addOns)) {
            $message->set(WisataMessage::FIELD_ADD_ONS, self::getAddOnsFormat($this->request->addOns));
        }
        $this->sendToCore($message);
        BookResponseParser::instance()->parse($message)->into($this);
    }

    public static function getAddOnsFormat($data)
    {
        $dataReturn = "";
        foreach ($data as  $value) {
            $dataReturn .= $value . ";";
        }
        return rtrim($dataReturn, ";");
    }

    /* Handle 2017-012-20 from Client :-( */
    public static function getDepartureDate($date)
    {
        $arr = explode('-', $date);
        return (strlen($arr[1]) === 3) ? $arr[0] . "-" . substr($arr[1], 1, 2) . "-" . $arr[2] : $date;
    }

    public static function getArrivalDate($date, $duration)
    {
        return date('Y-m-d', strtotime($date . ' +' . intval(explode("D", $duration)[0]) . ' day'));
    }
}