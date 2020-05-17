<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Utility;
use Phalcon\Db;

class DataPaymentController extends APIController
{

    protected $invoking = "Data Payment Flight";

    public function indexAction()
    {

        //$this->response->setDataAsObject();

        foreach ($this->db->fetchAll("SELECT * FROM transaksi t WHERE t.id_transaksi = ? UNION SELECT * FROM transaksi_backup tb WHERE tb.id_transaksi = ?", Db::FETCH_OBJ, [$this->request->transactionId, $this->request->transactionId]) as $value) {

            $departDates = self::getDates($value->bill_info13, $value->bill_info22);
            $arrivalDates = self::getDates($value->bill_info13, $value->bill_info23);
            $airport = (array) json_decode(Utility::$airport);
            $this->response->data[] = (object) array(
                'id_transaksi' => $value->id_transaksi,
                'kode_booking' => ($value->bill_info1 === null) ? '' : $value->bill_info1,
                'origin' => $airport[$value->bill_info11]->airportName,
                'destination' => $airport[$value->bill_info12]->airportName,
                'tanggal_keberangkatan' => trim(explode('|', $departDates)[1]),
                'hari_keberangkatan' => trim(explode(',', $departDates)[0]),
                'jam_keberangkatan' => $value->bill_info22,
                'tanggal_kedatangan' => trim(explode('|', $arrivalDates)[1]),
                'hari_kedatangan' => trim(explode(',', $arrivalDates)[0]),
                'jam_kedatangan' => $value->bill_info23,
                'kode_maskapai' => $value->bill_info15,
                'nama_maskapai' => Utility::getProductName($this, $value->id_produk),
                'penumpang' => self::getPassengers($value),
                'url_etiket' => 'http://api.Travel.co.id/app/generate_etiket?id_transaksi=' . $value->id_transaksi,
                'url_struk' => 'http://api.Travel.co.id/app/generate_struk?id_transaksi=' . $value->id_transaksi,
                'airlineIcon' => 'https://static.scash.bz/Travel/assets/images/flighticons/' . substr($value->id_produk, 2, 2) . '.png',
                'subClass' => $value->bill_info66,
                'nominal' => $value->nominal,
                'nominal_admin' => $value->nominal_admin,
                "duration" => Utility::getDurations($value->bill_info11, $value->bill_info12, $value->bill_info22, $value->bill_info23)
            );
        }
    }

    public static function getDates($strdate, $time)
    {
        return preg_replace(Utility::$pattern, Utility::$replace, date("l", strtotime($strdate)) . ",|" . date("d", strtotime($strdate)) . " " . date("F", strtotime($strdate)) . " " . date("Y", strtotime($strdate)) . " |" . $time);
    }

    public static function getPassengers($value)
    {
        $passenger = array();
        foreach (array($value->bill_info34, $value->bill_info35, $value->bill_info36, $value->bill_info37, $value->bill_info38, $value->bill_info39, $value->bill_info40, $value->bill_info41, $value->bill_info42, $value->bill_info43, $value->bill_info44, $value->bill_info45) as $value) {
            if (!empty($value)) {
                $temporary = explode(";", $value);
                $passenger[] = array("status" => ($temporary[0] === 'ADT') ? 'DEWASA' : (($temporary[0] === 'CHD') ? 'ANAK' : 'BAYI'), "nama" => $temporary[2] . " " . $temporary[3], "title" => $temporary[1]);
            }
        }
        return $passenger;
    }
}