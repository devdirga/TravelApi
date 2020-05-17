<?php

namespace Travel\TravelBus;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\TravelBus\BookResponseParser;
use Travel\Libraries\MTI;

class BookController extends APIController
{
    protected $invoking = "Book TravelBus";

    public function indexAction()
    {
        $this->setMTI(MTI::TAGIHAN);
        $this->setProductCode($this->request->produk);

        $message = new TravelBusMessage($this);

        $message->set(TravelBusMessage::FIELD_COMMAND, $this->request->command);
        $message->set(TravelBusMessage::FIELD_TRAVEL_CODE, $this->request->kodeAgent);
        $message->set(TravelBusMessage::FIELD_KOTA_BERANGKAT, $this->request->kotaBerangkat);
        $message->set(TravelBusMessage::FIELD_CABANG_BERANGKAT, $this->request->cabangBerangkat);
        $message->set(TravelBusMessage::FIELD_KOTA_TIBA, $this->request->kotaTiba);
        $message->set(TravelBusMessage::FIELD_CABANG_TIBA, $this->request->cabangTiba);
        $message->set(TravelBusMessage::FIELD_TANGGAL_BERANGKAT, $this->request->tanggalBerangkat);
        $message->set(TravelBusMessage::FIELD_WAKTU_BERANGKAT, $this->request->waktuBerangkat);
        $message->set(TravelBusMessage::FIELD_WAKTU_BERANGKAT_START, $this->request->waktuBerangkat);

        $message->set(TravelBusMessage::FIELD_PULANG_PERGI, $this->request->isPulangPergi);
        $message->set(TravelBusMessage::FIELD_JUMLAH_PENUMPANG, $this->request->jumlahPenumpang);
        $message->set(TravelBusMessage::FIELD_KODE_JURUSAN, $this->request->kodeJurusan);
        $message->set(TravelBusMessage::FIELD_ID_JURUSAN, $this->request->idJurusan);
        $message->set(TravelBusMessage::FIELD_KODE_JADWAL, $this->request->codeJadwal);
        $message->set(TravelBusMessage::FIELD_NOMINAL_ADMIN, $this->request->nominalAdmin);
        $message->set(TravelBusMessage::FIELD_TRAVEL_AGENT, $this->request->travelAgent);
        $message->set(TravelBusMessage::FIELD_LAYOUT_KURSI, $this->request->layoutKursi);
        $message->set(TravelBusMessage::FIELD_NO_KURSI, $this->request->noKursi);

        $message->set(TravelBusMessage::FIELD_NAMA_PEMESAN, $this->request->namaPemesan);
        $message->set(TravelBusMessage::FIELD_ALAMAT_PEMESAN, $this->request->alamatPemesan);
        $message->set(TravelBusMessage::FIELD_NO_HP_PEMESAN, $this->request->noHpPemesan);
        $message->set(TravelBusMessage::FIELD_EMAIL_PEMESAN, $this->request->emailPemesan);
        $message->set(TravelBusMessage::FIELD_PENUMPANG1, $this->request->penumpang1);
        $message->set(TravelBusMessage::FIELD_PENUMPANG2, $this->request->penumpang2);
        $message->set(TravelBusMessage::FIELD_PENUMPANG3, $this->request->penumpang3);
        $message->set(TravelBusMessage::FIELD_NOMINAL, $this->request->nominal);
        $message->set(TravelBusMessage::FIELD_NTA, $this->request->nta);

        $this->sendToCore($message);

        echo 'Response/', $message->toString(), PHP_EOL;

        BookResponseParser::instance()->parse($message)->into($this);
    }
}