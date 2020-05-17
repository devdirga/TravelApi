<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Travel\Libraries\Lists;

use Travel\Libraries\APIController;
use Travel\Libraries\Utility;
use Phalcon\Mvc\Controller;
use Phalcon\Db;

/**
 * Description of Tets
 *
 * @author bimasakti
 */
class ListKERETA extends Controller
{

    protected $invoking = "Train List";

    public static function init($implodedata)
    {
        return explode("#", $implodedata);
    }

    public static function initObject(APIController $apiController, $data)
    {
        $sqlLastSeat = $apiController->db->query("SELECT bill_info83 FROM transaksi WHERE id_produk in('TKAI','WKAI') and bill_info1= ? and id_outlet= ? order by id_transaksi desc limit 1", [$data->bill_info1, $data->id_outlet]);

        $sqlLastSeat->setFetchMode(Db::FETCH_OBJ);
        $kursi = $sqlLastSeat->fetch();
        return (object) array(
            "additional" => array(
                array("key" => "Kode Booking", "value" => $data->bill_info2),
                array("key" => "Nama Kereta", "value" => $data->bill_info78),
                array("key" => "Rute", "value" => $data->bill_info79 . "(" . $data->bill_info11 . ") - " . $data->bill_info81 . " (" . $data->bill_info12 . ")"),
                array("key" => "Tanggal Keberangkatan", "value" => self::getFullDate_time($data->bill_info13)),
                array("key" => "Waktu Keberangkatan", "value" => substr($data->bill_info80, 0, 2) . ":" . substr($data->bill_info80, 2, 2)),
                array("key" => "Waktu Kedatangan", "value" => substr($data->bill_info82, 0, 2) . ":" . substr($data->bill_info82, 3, 2)),
                array("key" => "Kursi", "value" => $kursi->bill_info83),
            ),
            "url_etiket" => "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $data->id_transaksi,
            "url_struk" => "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $data->id_transaksi,
            "url_image" => "http://static.scash.bz/jadipergi/img/wisata/FA53732/21112016_9313554dd92b6aff2bfe8e09ebc865ca.jpg"
        );
    }

    public function initBook(APIController $apiController)
    {
        $result = array();

        $_m = array();
        $__m = array();
        foreach ($this->db->fetchAll("SELECT jenis_transaksi,* FROM transaksi "
            . "WHERE jenis_transaksi = 0 "
            . "AND id_outlet = '" . $apiController->getOutletId() . "' "
            . "AND id_produk IN ('WKAI','TKAI') "
            . "AND response_code = '00' "
            . "AND time_response + interval '" . Utility::getBookExpiredTime($apiController->request->product) . "' >= NOW() ORDER BY time_request DESC ", Db::FETCH_OBJ) as $value) {
            if (!array_key_exists($value->bill_info2, $_m)) {
                $_m[$value->bill_info2] = $value->id_transaksi;
            }
        }
        foreach ($_m as $_mVal) {
            $__m[] = $_mVal;
        }

        if (sizeof($__m, TRUE) > 0) {
            foreach ($this->db->fetchAll("SELECT * FROM transaksi WHERE id_transaksi in (" . implode(",", $__m) . ") and bill_info2 not in(select bill_info2 from transaksi where id_outlet='" . $value->id_outlet . "' and id_produk in('TKAI','WKAI') and (keterangan like 'CANCEL BOOKING BERHASIL' or keterangan LIKE '%already canceled%'))", Db::FETCH_OBJ) as $value) {

                $timeRequest = date('Y-m-d H:i:s', strtotime($value->time_request));
                $timeLimit = date('Y-m-d H:i:s', strtotime('+10 minute', strtotime($timeRequest)));
                $result[] = (object) array(
                    "id_transaksi" => $value->id_transaksi,
                    "kode_booking" => $value->bill_info2,
                    "origin" => $value->bill_info79,
                    "destination" => $value->bill_info81,
                    "tanggal_keberangkatan" => $value->bill_info13,
                    "hari_keberangkatan" => $this->getDay($value->bill_info13),
                    "jam_keberangkatan" => $value->bill_info80,
                    "tanggal_kedatangan" => "",
                    "hari_kedatangan" => "",
                    "jam_kedatangan" => $value->bill_info82,
                    "kode_kereta" => $value->bill_info83,
                    "nama_kereta" => $value->bill_info78,
                    "classes" => $value->bill_info54,
                    "penumpang" => self::getPassengerKereta($value),
                    "komisi" => Utility::getKomisi($apiController, $value->id_transaksi),
                    "expiredDate" => $timeLimit
                );
            }
        }


        return $result;
    }

    /*
      public function initBook(APIController $apiController) {
      $outlet_id = $apiController->getOutletId();
      $result = array();
      $expiredTime = Utility::getBookExpiredTime($apiController->request->product);
      $query = $apiController->db->query("SELECT jenis_transaksi,* FROM transaksi
      WHERE id_transaksi IN (
      SELECT max(id_transaksi) FROM transaksi
      WHERE id_outlet = ? AND id_produk = 'TKAI' AND response_code = '00' AND time_response + interval '".$expiredTime."' >= now()
      GROUP BY bill_info2
      ) ORDER BY time_request DESC", [$outlet_id]);

      $query->setFetchMode(Db::FETCH_OBJ);

      foreach ($query->fetchAll() as $value)
      {
      if($value->jenis_transaksi == "0" && ($value->keterangan !== 'CANCEL BOOKING BERHASIL')){
      $result[] = (object) array(
      "id_transaksi" => $value->id_transaksi,
      "kode_booking" => $value->bill_info2,
      "origin" => $value->bill_info79,
      "destination" => $value->bill_info81,
      "tanggal_keberangkatan" => $value->bill_info13,
      "hari_keberangkatan" => $this->getDay($value->bill_info13),
      "jam_keberangkatan" => $value->bill_info80,
      "tanggal_kedatangan" => "",
      "hari_kedatangan" => "",
      "jam_kedatangan" => $value->bill_info82,
      "kode_kereta" => $value->bill_info83,
      "nama_kereta" => $value->bill_info78,
      "classes" => $value->bill_info54,
      "penumpang" => Utility::getPassengerKereta($value),
      "komisi" =>  Utility::getKomisi($apiController,$value->id_transaksi),
      "url_etiket" => "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $value->id_transaksi,
      "url_struk" => "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $value->id_transaksi,
      );
      }
      }

      return $result;
      }
     * 
     * */

    public function initPAYMENT(APIController $apiController)
    {

        $result = array();

        $query = $apiController->db->query("(SELECT * FROM fmss.transaksi WHERE
                fmss.transaksi.jenis_transaksi = 1 AND fmss.transaksi.response_code='00' AND
                fmss.transaksi.id_produk like '%KAI%' AND fmss.transaksi.id_outlet = ? 
                ORDER BY time_request DESC) 
                union
                (SELECT * FROM fmss.transaksi_backup WHERE
                fmss.transaksi_backup.jenis_transaksi = 1 AND fmss.transaksi_backup.response_code='00' AND
                fmss.transaksi_backup.id_produk like '%KAI%' AND fmss.transaksi_backup.id_outlet = ? 
                ORDER BY time_request DESC LIMIT ?) ", [$apiController->getOutletId(), $apiController->getOutletId(), 20]);

        $query->setFetchMode(Db::FETCH_OBJ);

        foreach ($query->fetchAll() as $value) {
            $result[] = (object) array(
                "id_transaksi" => $value->id_transaksi,
                "kode_booking" => $value->bill_info2,
                "origin" => $value->bill_info79,
                "destination" => $value->bill_info81,
                "tanggal_keberangkatan" => $value->bill_info13,
                "hari_keberangkatan" => "",
                "jam_keberangkatan" => $value->bill_info80,
                "tanggal_kedatangan" => "",
                "hari_kedatangan" => "",
                "jam_kedatangan" => $value->bill_info82,
                "kode_kereta" => $value->bill_info83,
                "nama_kereta" => $value->bill_info78,
                "classes" => $value->bill_info54,
                "penumpang" => self::getPassengerKereta($value),
                "url_etiket" => "http://api.Travel.co.id/app/generate_etiket?id_transaksi=" . $value->id_transaksi,
                "url_struk" => "http://api.Travel.co.id/app/generate_struk?id_transaksi=" . $value->id_transaksi,
            );
        }

        return $result;
    }

    public function initStatus($bookCode)
    {
        $query = $this->db->query("SELECT * FROM transaksi WHERE bill_info2 = ? and response_code = '00' order by time_request DESC LIMIT 1", [$bookCode]);
        $query->setFetchMode(Db::FETCH_OBJ);
        $data = $query->fetch();

        if (!$data) {
            return (object) array("Status" => "Kode booking tidak ditemukan", "bookCode" => $bookCode);
        }

        return (object) array(
            "bookCode" => $data->bill_info2,
            "Produk" => Utility::getProductName($this, $data->id_produk),
            "Status" => (intval($data->jenis_transaksi) == 0) ? "Booking" : "Payment"
        );
    }

    public function getDay($strdate)
    {
        $timestamp = strtotime($strdate);
        return preg_replace(Utility::getPattern(), Utility::getReplace(), date("l", $timestamp));
    }

    public function getFullDate($strdate, $time)
    {
        $timestamp = strtotime($strdate);
        return preg_replace(Utility::getPattern(), Utility::getReplace(), date("l", $timestamp) . "," . date("d", $timestamp) . " " . date("F", $timestamp) . " " . date("Y", $timestamp));
    }

    public static function getFullDate_time($strdate)
    {
        $timestamp = strtotime($strdate);
        return preg_replace(Utility::getPattern(), Utility::getReplace(), date("l", $timestamp) . "," . date("d", $timestamp) . " " . date("F", $timestamp) . " " . date("Y", $timestamp));
    }

    //    public static function getPassengerKereta($value) {
    //        $penumpang = array();
    //        $list_penumpang = array($value->bill_info20,
    //            $value->bill_info24,
    //            $value->bill_info28,
    //            $value->bill_info32
    //        );
    //        for ($i = 0; $i <= 3; $i++) {
    //            if (!$list_penumpang[$i]) {
    //                break;
    //            }
    //
    //            $penumpang[$i] = array("nama" => $list_penumpang[$i]);
    //        }
    //        return $penumpang;
    //    }

    public static function getPassengerKereta($value)
    {
        $penumpang = array();
        $list_penumpang = array(
            $value->bill_info20,
            $value->bill_info24,
            $value->bill_info28,
            $value->bill_info32,
            $value->bill_info40,
            $value->bill_info42,
            $value->bill_info44
        );
        for ($i = 0; $i <= sizeof($list_penumpang) - 1; $i++) {

            if ($i >= 4 && (str_replace("#", "", $list_penumpang[$i]) === "" || str_replace("#", "", $list_penumpang[$i]) === ";")) {

                continue;
            } else if ($i >= 4 && str_replace("#", "", $list_penumpang[$i]) !== "") {
            } else if (!$list_penumpang[$i]) {

                continue;
            }

            if (strpos($list_penumpang[$i], '#') !== false) {

                $penumpang[] = array("nama" => explode('#', $list_penumpang[$i])[0]);
            } else {
                $penumpang[] = array("nama" => $list_penumpang[$i]);
            }
        }
        return $penumpang;
    }
}