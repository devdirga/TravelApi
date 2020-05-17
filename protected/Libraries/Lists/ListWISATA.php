<?php

namespace Travel\Libraries\Lists;

use Travel\Libraries\APIController;
use Travel\Libraries\Utility;
use Phalcon\Mvc\Controller;
use Phalcon\Db;

class ListWISATA extends Controller
{

    protected $invoking = "List Wisata";

    public static function init($implodedata)
    {
        $data = explode("#", $implodedata);
        return $data;
    }

    public function initObject($data)
    {
        return (object) array(
            "additional" => array(
                array("key" => "Kode Booking", "value" => $data->bill_info5),
                array("key" => "Lokasi Wisata", "value" => $data->bill_info16),
                array("key" => "Tanggal Mulai", "value" => $this->getFullDate($data->bill_info24)),
                array("key" => "Tanggal Selesai", "value" => $this->getFullDate($data->bill_info25)),
                array("key" => "Jumlah Peserta", "value" => $data->bill_info32 . " Orang")
            ),
            "url_etiket" => "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $data->id_transaksi,
            "url_struk" => "http://api.Travel.co.id/app/generate_struk?transaction_id=" . $data->id_transaksi,
            "url_image" => "http://static.scash.bz/jadipergi/img/wisata/FA53732/21112016_9313554dd92b6aff2bfe8e09ebc865ca.jpg"
        );
        //return $data;
    }

    public function initBook(APIController $apiController)
    {

        $outlet_id = $apiController->getOutletId();
        $result = array();
        $expiredTime = Utility::getBookExpiredTime($apiController->request->product);
        $query = $apiController->db->query("SELECT * FROM transaksi
            WHERE id_transaksi IN (
                SELECT max(id_transaksi) FROM transaksi 
                WHERE id_outlet = ? AND id_produk = 'TOURNEW' AND response_code = '00' AND time_response + interval '" . $expiredTime . "' >= now()
                GROUP BY bill_info43
            ) ORDER BY time_request DESC", [$outlet_id]);

        $query->setFetchMode(Db::FETCH_OBJ);

        foreach ($query->fetchAll() as $value) {
            if ($value->jenis_transaksi == "0") {

                $timeRequest = date('Y-m-d H:i:s', strtotime($value->time_request));
                $timeLimit = date('Y-m-d H:i:s', strtotime('+2 hour', strtotime($timeRequest)));

                $result[] = (object) array(
                    "id_transaksi" => $value->id_transaksi,
                    "kode_booking" => $value->bill_info5,
                    "destination" => $value->bill_info16,
                    "tanggal_mulai" => $this->normalisasiTanggal($value->bill_info24),
                    "tangal_selesai" => $this->normalisasiTanggal($value->bill_info25),
                    "nama_peserta" => $value->bill_info2,
                    "Jumlah Peserta" => $value->bill_info32,
                    "lama" => $value->bill_info12,
                    "harga" => $value->nominal,
                    "wisatawan" => $this->getWisatawan($value),
                    "komisi" =>  Utility::getKomisi($apiController, $value->id_transaksi),
                    "url_etiket" => "http//api.Travel.co.id/app/generate_etiket?id_transaksi=" . $value->id_transaksi,
                    "url_struk" => "http//api.Travel.co.id/app/generate_struk?id_transaksi=" . $value->id_transaksi,
                    "url_image" => "http//static.travel.com/maskapai/logo-garuda.png",
                    "expiredDate" => $timeLimit
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
                fmss.transaksi.id_produk like 'TOUR%' AND fmss.transaksi.id_outlet = ?   
                ORDER BY time_request DESC LIMIT ? ", [$apiController->getOutletId(), 10]);

        $query->setFetchMode(Db::FETCH_OBJ);

        foreach ($query->fetchAll() as $value) {
            $result[] = (object) array(
                "id_transaksi" => $value->id_transaksi,
                "kode_booking" => $value->bill_info5,
                "destination" => $value->bill_info16,
                "tanggal_mulai" => $this->normalisasiTanggal($value->bill_info24),
                "tangal_selesai" => $this->normalisasiTanggal($value->bill_info25),
                "nama_peserta" => $value->bill_info2,
                "Jumlah Peserta" => $value->bill_info32,
                "lama" => $value->bill_info12,
                "harga" => $value->nominal,
                "wisatawan" => $this->getWisatawan($value),
                "url_etiket" => "http=>//api.Travel.co.id/app/generate_etiket?id_transaksi=" . $value->id_transaksi,
                "url_struk" => "http=>//api.Travel.co.id/app/generate_struk?id_transaksi=" . $value->id_transaksi,
                "url_image" => "http=>//static.travel.com/maskapai/logo-garuda.png"
            );
        }
        return $result;
    }

    public function getFullDate($strdate)
    {
        //menormalkan tanggal
        $arr = explode("-", $strdate);
        $strdate = intval($arr[0]) . "-" . intval($arr[1]) . "-" . intval($arr[2]);

        $timestamp = strtotime($strdate);
        return preg_replace(Utility::getPattern(), Utility::getReplace(), date("l", $timestamp) . "," . date("d", $timestamp) . " " . date("F", $timestamp) . " " . date("Y", $timestamp));
    }
    public function getWisatawan($value)
    {
        $guest = array();
        $guest[0] = array(
            "nama" => $value->bill_info30,
            "phone" => $value->bill_info31
        );

        return $guest;
    }

    public function normalisasiTanggal($strdate)
    {
        $arr = explode("-", $strdate);
        return intval($arr[0]) . "-" . intval($arr[1]) . "-" . intval($arr[2]);
    }
}