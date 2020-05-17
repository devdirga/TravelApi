<?php

namespace Travel\Libraries\Lists;

use Travel\Libraries\APIController;
use Travel\Libraries\Utility;
use Phalcon\Mvc\Controller;
use Phalcon\Db;

class ListTRAVEL extends Controller
{

    protected $invoking = "List Travel";

    public static function init($implodedata)
    {
        $data = explode("#", $implodedata);

        return $data;
    }

    public function initObject($data)
    {
        return (object) array(
            "additional" => array(
                array("key" => "Kode Booking", "value" => $data->bill_info1),
                array("key" => "Nama Agent", "value" => $data->bill_info10),
                array("key" => "Rute", "value" => $data->bill_info11 . " (" . $data->bill_info12 . ") - " . $data->bill_info13 . " (" . $data->bill_info14 . ")"),
                array("key" => "Waktu Keberangkatan", "value" => $this->getFullDate($data->bill_info15, $data->bill_info16)),
                array("key" => "Waktu Kedatangan", "value" => "-")
            ),
            "url_etiket" => "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $data->id_transaksi,
            "url_struk" => "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $data->id_transaksi,
            "url_image" => "http://static.scash.bz/jadipergi/img/wisata/FA53732/21112016_9313554dd92b6aff2bfe8e09ebc865ca.jpg"
        );
        //return $data;
    }

    public static function initBook(APIController $apiController)
    {

        $outlet_id = $apiController->getOutletId();
        $result = array();
        $expiredTime = Utility::getBookExpiredTime($apiController->request->product);
        $query = $apiController->db->query("SELECT * FROM transaksi
            WHERE id_transaksi IN (
                SELECT max(id_transaksi) FROM transaksi 
                WHERE id_outlet = ? AND id_produk like 'TUX%' AND response_code = '00' AND time_response + interval '" . $expiredTime . "' >= now()
                GROUP BY bill_info2
            ) ORDER BY time_request DESC", [$outlet_id]);

        $query->setFetchMode(Db::FETCH_OBJ);

        foreach ($query->fetchAll() as $value) {
            if ($value->jenis_transaksi == "0") {
                $result[] = (object) array(
                    "id_transaksi" => $value->id_transaksi,
                    "kode_booking" => $value->bill_info1,
                    "nama_travel" => $value->bill_info24,
                    "origin" => $value->bill_info12 . "(" . $value->bill_info11 . ")",
                    "destination" => $value->bill_info14 . "(" . $value->bill_info13 . ")",
                    "tanggal_keberangkatan" => $value->bill_info15,
                    "hari_keberangkatan" => $this->getDay($value->bill_info15),
                    "jam_keberangkatan" => substr($value->bill_info16, 0, 2) . ":" . substr($value->bill_info16, 2, 2),
                    "tanggal_kedatangan" => $value->bill_info15,
                    "hari_kedatangan" => $this->getDay($value->bill_info15),
                    "jam_kedatangan" => "",
                    "komisi" =>  Utility::getKomisi($apiController, $value->id_transaksi),
                    "penumpang" => $this->getPassengerTravel($value),
                    "url_etiket" => "http//api.Travel.co.id/app/generate_etiket?id_transaksi=" . $value->id_transaksi,
                    "url_struk" => "http//api.Travel.co.id/app/generate_struk?id_transaksi=" . $value->id_transaksi,
                    "airlineIcon" => "http//static.travel.com/maskapai/logo-garuda.png",
                );
            }
        }

        return $result;
    }
    public function initPayment(APIController $apiController)
    {

        $result = array();

        $query = $apiController->db->query("SELECT * FROM fmss.transaksi WHERE
                fmss.transaksi.jenis_transaksi = 1 AND fmss.transaksi.response_code='00' AND
                fmss.transaksi.id_produk like 'TUX%' AND fmss.transaksi.id_outlet = ?  
                ORDER BY time_request DESC LIMIT ? ", [$apiController->getOutletId(), 10]);

        $query->setFetchMode(Db::FETCH_OBJ);

        $i = 0;

        foreach ($query->fetchAll() as $value) {
            $result[$i] = (object) array(
                "id_transaksi" => $value->id_transaksi,
                "kode_booking" => $value->bill_info1,
                "nama_travel" => $value->bill_info24,
                "origin" => $value->bill_info12 . "(" . $value->bill_info11 . ")",
                "destination" => $value->bill_info14 . "(" . $value->bill_info13 . ")",
                "tanggal_keberangkatan" => $value->bill_info15,
                "hari_keberangkatan" => $this->getDay("2017-08-05"),
                "jam_keberangkatan" => substr($value->bill_info16, 0, 2) . ":" . substr($value->bill_info16, 2, 2),
                "tanggal_kedatangan" => $value->bill_info15,
                "hari_kedatangan" => $this->getDay($value->bill_info15),
                "jam_kedatangan" => "",
                "penumpang" => $this->getPassengerTravel($value),
                "url_etiket" => "http=>//api.Travel.co.id/app/generate_etiket?id_transaksi=" . $value->id_transaksi,
                "url_struk" => "http=>//api.Travel.co.id/app/generate_struk?id_transaksi=" . $value->id_transaksi,
                "airlineIcon" => "http=>//static.travel.com/maskapai/logo-garuda.png"
            );
            $i++;
        }

        return $result;
    }
    public function getFullDate($strdate, $time)
    {
        $timestamp = strtotime($strdate);
        return preg_replace(Utility::getPattern(), Utility::getReplace(), date("l", $timestamp) . "," . date("d", $timestamp) . " " . date("F", $timestamp) . " " . date("Y", $timestamp) . " " . $time);
    }

    public function getDay($strdate)
    {
        $timestamp = strtotime($strdate);
        return preg_replace(Utility::getPattern(), Utility::getReplace(), date("l", $timestamp));
    }
    public function getPassengerTravel($value)
    {
        $penumpang = array();
        $list_penumpang = array(
            $value->bill_info40,
            $value->bill_info41,
            $value->bill_info42
        );
        for ($i = 0; $i <= 3; $i++) {
            if (!$list_penumpang[$i])
                break;
            $penumpang[$i] = array("nama" => $list_penumpang[$i]);
        }
        return $penumpang;
    }
}