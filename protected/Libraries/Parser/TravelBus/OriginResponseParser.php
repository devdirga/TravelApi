<?php

namespace Travel\Libraries\Parser\TravelBus;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;

class OriginResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * TravelBus message response from core.
     * 
     * @var TravelBusMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $Origination = array();
        $Country = $apiController->db->query("select distinct nama_kota as nama_kota , nama_kota as nama_negara, nama_kota as kode_negara_fp  from tiketux_mt_kota where nama_kota in(select distinct nama_kota from tiketux_mt_cabang ) order by 1")->fetchAll();
        foreach ($Country as $row) {
            array_push(
                $Origination,
                array(
                    "nama_negara" => $row["nama_negara"],
                    "id_negara" => $row["kode_negara_fp"],
                    "daftar_kota" => $apiController->db->query("select 2 as no, nama_cabang as kode_kota, nama_cabang as nama_kota from tiketux_mt_cabang where upper(nama_kota) like upper(?) order by 2", [$row["nama_kota"]])->fetchAll()
                )
            );
        }
        if (count($Origination) > 0) {
            $apiController->response->data = $Origination;
        } else {
            $apiController->response->setStatus("01", "Origin is empty.");
        }
    }
}