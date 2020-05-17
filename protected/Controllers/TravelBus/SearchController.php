<?php

namespace Travel\TravelBus;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\MTI;
use Phalcon\Db;

class SearchController extends APIController
{
    protected $invoking = "Search TravelBus";

    const iconUrl = 'http://static.scash.bz/jadipergi/static/new_template/images/travel/';

    public function indexAction()
    {
        $schedules = $this->db->fetchAll(
            "SELECT a.*,b.nama FROM tiketux_mt_jurusan a 
                LEFT JOIN tiketux_mt_agen b ON a.kode_agen = b.kode_agen WHERE 
                (UPPER(a.cabang_asal)=UPPER(?) OR UPPER(a.kota_asal)=upper(?)) AND
                (UPPER(a.cabang_tujuan)=UPPER(?) OR UPPER(a.kota_tujuan)=UPPER(?))",
            Db::FETCH_OBJ,
            [$this->request->departure, $this->request->departure, $this->request->arrival, $this->request->arrival]
        );

        $data = array();

        $exclude = array("CPG", "XTR");
        $include = array("BRT");

        foreach ($schedules as $schedule) {

            //if(!in_array($schedule->kode_agen, $exclude))
            if (in_array($schedule->kode_agen, $include)) {
                $this->setMTI(MTI::DATA);
                $this->setProductCode($this->request->produk);

                $message = new TravelBusMessage($this);

                $message->set(TravelBusMessage::FIELD_TANGGAL_BERANGKAT, $this->request->departureDate);
                $message->set(TravelBusMessage::FIELD_TRAVEL_CODE, $schedule->kode_agen);
                $message->set(TravelBusMessage::FIELD_ID_JURUSAN, $schedule->id_jurusan);
                $message->set(TravelBusMessage::FIELD_COMMAND, $this->request->command);

                $this->sendToCore($message);

                if ($message->get(TravelBusMessage::FIELD_STATUS) === '00') {

                    $msg = json_decode($message->get(TravelBusMessage::FIELD_MESSAGE));

                    foreach ($msg as $m) {
                        $m->kode_agen = $schedule->kode_agen;
                        $m->id_jurusan = $schedule->id_jurusan;
                        $m->nama = $schedule->nama;
                        $m->kota_asal = $schedule->kota_asal;
                        $m->kota_asal = $schedule->kota_asal;
                        $m->kota_tujuan = $schedule->kota_tujuan;
                        $m->cabang_asal = $schedule->cabang_asal;
                        $m->cabang_tujuan = $schedule->cabang_tujuan;
                        $m->kode_jurusan = $schedule->kode_jurusan;
                        $m->nominal_admin = $schedule->nominal_admin;
                        $m->icon = SearchController::iconUrl . $schedule->kode_agen . '.png';
                        $m->promo = (string) $schedule->promo;

                        $data[] = $m;
                    }
                }
            }
        }

        $this->response->data = $data;
    }
}