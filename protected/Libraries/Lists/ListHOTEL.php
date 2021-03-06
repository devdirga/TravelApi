<?php

namespace Travel\Libraries\Lists;

use Travel\Libraries\APIController;
use Travel\Libraries\Utility;
use Phalcon\Mvc\Controller;
use Phalcon\Db;

class ListHOTEL extends Controller
{

    protected $invoking = "List Hotel";

    public static function init($implodedata)
    {
        $data = explode("#", $implodedata);
        return $data;
    }

    public function initObject($data)
    {
        return (object) array(
            "additional" => array(
                array("key" => "Kode Booking", "value" => $data->bill_info2),
                array("key" => "Nama Hotel", "value" => $data->bill_info26),
                array("key" => "Type Kamar", "value" => $data->bill_info15),
                array("key" => "Tanggal Check-In", "value" => $this->getFullDate($data->bill_info33)),
                array("key" => "Tanggal Check-Out", "value" => $this->getFullDate($data->bill_info34)),
                array("key" => "Lama menginap", "value" => $data->bill_info35 . " malam")
            ),
            "url_etiket" => "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $data->id_transaksi . "&id_outlet=" . $data->id_outlet,
            "url_struk" => "http://api.Travel.co.id/app/generate_struk?transaction_id=" . $data->id_transaksi,
            "url_image" => "http://static.scash.bz/jadipergi/img/wisata/FA53732/21112016_9313554dd92b6aff2bfe8e09ebc865ca.jpg"
        );
    }

    public function initBook(APIController $apiController)
    {
        $outlet_id = $apiController->getOutletId();
        $result = array();
        $expiredTime = Utility::getBookExpiredTime($apiController->request->product);
        $query = $apiController->db->query("SELECT * FROM transaksi
            WHERE id_transaksi IN (
                SELECT max(id_transaksi) FROM transaksi 
                WHERE id_outlet = ? AND id_produk = 'RHOTEL' AND response_code = '00' AND time_response + interval '" . $expiredTime . "' >= now()
                GROUP BY bill_info2
            ) ORDER BY time_request DESC", [$outlet_id]);

        $query->setFetchMode(Db::FETCH_OBJ);

        foreach ($query->fetchAll() as $value) {
            $timeRequest = date('Y-m-d H:i:s', strtotime($value->time_request));
            $timeLimit = date('Y-m-d H:i:s', strtotime('+2 hour', strtotime($timeRequest)));

            if ($value->jenis_transaksi == "0") {
                $result[] = (object) array(
                    "id_transaksi" => $value->id_transaksi,
                    "kode_booking" => $value->bill_info2,
                    "nama_hotel" => $value->bill_info26,
                    "room_type" => $value->bill_info15,
                    "pax" => $value->bill_info6,
                    "check_in" => $value->bill_info33,
                    "check_out" => $value->bill_info34,
                    "tamu" => $this->getGuestHotel($value),
                    "komisi" =>  Utility::getKomisi($apiController, $value->id_transaksi),
                    "url_etiket" => "http//api.Travel.co.id/app/generate_etiket?id_transaksi=" . $value->id_transaksi . "&id_outlet=" . $outlet_id,
                    "url_struk" => "http//api.Travel.co.id/app/generate_struk?id_transaksi=" . $value->id_transaksi,
                    "duration" => $value->bill_info35 . " malam",
                    "expiredDate" => $timeLimit
                );
            }
        }

        return $result;
    }

    public function initPayment(APIController $apiController)
    {
        $result = array();

        $query = $apiController->db->query("SELECT * FROM fmss.transaksi
                LEFT JOIN fmss.transaksi_reservasi_hotel ON fmss.transaksi.id_transaksi = fmss.transaksi_reservasi_hotel.id_transaksi 
                WHERE
                fmss.transaksi.jenis_transaksi = 1 AND fmss.transaksi.response_code='00' AND
                fmss.transaksi.id_produk like '%HOTEL%' AND fmss.transaksi.id_outlet = ?  
                ORDER BY time_request DESC LIMIT ? ", [$apiController->getOutletId(), 10]);

        $query->setFetchMode(Db::FETCH_OBJ);

        $i = 0;

        foreach ($query->fetchAll() as $value) {
            $result[$i] = (object) array(
                "id_transaksi" => $value->id_transaksi,
                "kode_booking" => $value->bill_info2,
                "nama_hotel" => $value->hotel_name,
                "room_type" => $value->bill_info15,
                "pax" => $value->bill_info6,
                "check_in" => $value->bill_info33,
                "check_out" => $value->bill_info34,
                "tamu" => $this->getGuestHotel($value),
                "url_etiket" => "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $value->id_transaksi . "&id_outlet=" . $value->id_outlet,
                "url_struk" => "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $value->id_transaksi,
                "duration" => $value->bill_info35 . " malam"
            );

            $i++;
        }

        return $result;
    }

    public function getFullDate($strdate)
    {
        $timestamp = strtotime($strdate);
        return preg_replace(Utility::getPattern(), Utility::getReplace(), date("l", $timestamp) . "," . date("d", $timestamp) . " " . date("F", $timestamp) . " " . date("Y", $timestamp));
    }
    public function getGuestHotel($value)
    {
        $guest = array();
        $guest[0] = array(
            "nama depan" => $value->bill_info41,
            "nama akhir" => $value->bill_info42
        );

        return $guest;
    }
}