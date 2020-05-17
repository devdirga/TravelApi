<?php

namespace Travel\Libraries\Parser\Wisata;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\WisataMessage;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Utility;
use Phalcon\Db;

class DetailResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * TravelBus message response from core.
     * 
     * @var WisataMessage
     */
    protected $message;
    protected $url_foto = "http://best.Travel.co.id/";
    protected $bb_url_foto = "https://static.scash.bz/jadipergi/img/wisata/";

    protected static $table_paket_wisata_2_mt_destinasi = 'paket_wisata_2_mt_destinasi';

    public function into(APIController $apiController)
    {
        $upPrice = 0;
        $detail = $apiController->db->query("SELECT pw.* , mp.nama_propinsi FROM paket_wisata_2_mt_destinasi pw LEFT JOIN mt_propinsi mp ON pw.id_propinsi = mp.id_propinsi WHERE pw.id_destinasi = ? ", [$apiController->request->idDestination])->fetch();

        $photos = array();

        $Photos = $apiController->db->query("select * from paket_wisata_2_foto_destinasi where id_destinasi=? order by urutan asc", [$apiController->request->idDestination])->fetchAll();

        foreach ($Photos as $value) {
            if (intval($value['use_cdn']) === 1) {
                array_push($photos, $this->bb_url_foto . $value['path_foto']);
            } else if (intval($value['use_cdn']) === 0) {
                array_push($photos, $this->url_foto . $value['path_foto']);
            } else if ($value['use_cdn'] == -1) {
                //nothing...
            }
        }

        //        WITH UP HARGA  
        //        $upPrice = $apiController->db->query("select nilai from global_setting where kunci =?",['setting_up_harga_paket_wisata_jadipergi'])->fetch()['nilai'];
        //        $price = $apiController->db->query("select custom_harga_1 + (custom_harga_1::float / 100::float * ?)::int as custom_harga_1,
        //                custom_harga_2 + (custom_harga_2::float / 100::float * ?)::int as custom_harga_2,
        //                custom_harga_3 + (custom_harga_3::float / 100::float * ?)::int as custom_harga_3,
        //                custom_harga_4 + (custom_harga_4::float / 100::float * ?)::int as custom_harga_4,
        //                custom_harga_5 + (custom_harga_5::float / 100::float * ?)::int as custom_harga_5,
        //                custom_harga_6 + (custom_harga_6::float / 100::float * ?)::int as custom_harga_6,
        //                custom_harga_7 + (custom_harga_7::float / 100::float * ?)::int as custom_harga_7,
        //                custom_harga_8 + (custom_harga_8::float / 100::float * ?)::int as custom_harga_8               
        //                from paket_wisata_2_harga_paket where tanggal::date > now()::date and status = 1 and id_destinasi = ? order by tanggal asc",
        //                [$upPrice,$upPrice,$upPrice,$upPrice,$upPrice,$upPrice,$upPrice,$upPrice,$apiController->request->idDestination])->fetchAll();

        $price = $apiController->db->query(
            "select custom_harga_1 as custom_harga_1,
                custom_harga_2 as custom_harga_2,
                custom_harga_3 as custom_harga_3,
                custom_harga_4 as custom_harga_4,
                custom_harga_5 as custom_harga_5,
                custom_harga_6 as custom_harga_6,
                custom_harga_7 as custom_harga_7,
                custom_harga_8 as custom_harga_8,               
                custom_harga_9 as custom_harga_9,              
                custom_harga_10 as custom_harga_10               
                from paket_wisata_2_harga_paket where tanggal::date > now()::date and status = 1 and id_destinasi = ? order by tanggal asc",
            [$apiController->request->idDestination]
        )->fetchAll();

        $typeWisata = $apiController->db->query("select m.nama_tipe_wisata from paket_wisata_2_data_tipe_wisata t
                left join  paket_wisata_2_mt_tipe_wisata m on(t.id_tipe_wisata=m.id_tipe_wisata)
                where t.id_destinasi=?", [$apiController->request->idDestination])->fetchAll();

        $facilities = $apiController->db->query("select m.nama_fasilitas from paket_wisata_2_fasilitas_include t
                left join  paket_wisata_2_mt_fasilitas m on(t.id_fasilitas=m.id_fasilitas)
                where t.id_destinasi=?", [$apiController->request->idDestination])->fetchAll();

        $object = $apiController->db->query("select nama_objek_wisata from paket_wisata_2_objek_wisata
                where id_destinasi=? limit 12", [$apiController->request->idDestination])->fetchAll();

        $facilitiesExclude = $apiController->db->query(
            "select * from paket_wisata_2_mt_fasilitas
                where id_fasilitas in(select id_fasilitas from paket_wisata_2_fasilitas_exclude where id_destinasi=?)",
            [$apiController->request->idDestination]
        )->fetchAll();

        $iteneraryQuery = $apiController->db->query("select nama_itinerary as title,detail_itinerary as content from paket_wisata_2_data_itinerary where id_destinasi=? order by urutan asc", [$apiController->request->idDestination]);
        $iteneraryQuery->setFetchMode(Db::FETCH_OBJ);
        $iten = $iteneraryQuery->fetch();
        $iten->content = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(($iten->content))))));



        //         WITH up harga
        //        $priceList = $apiController->db->query("select custom_harga_1 + (custom_harga_1::float / 100::float * ?)::int as custom_harga_1,
        //                custom_harga_2 + (custom_harga_2::float / 100::float * ?)::int as custom_harga_2,
        //                custom_harga_3 + (custom_harga_3::float / 100::float * ?)::int as custom_harga_3,
        //                custom_harga_4 + (custom_harga_4::float / 100::float * ?)::int as custom_harga_4,
        //                custom_harga_5 + (custom_harga_5::float / 100::float * ?)::int as custom_harga_5,
        //                custom_harga_6 + (custom_harga_6::float / 100::float * ?)::int as custom_harga_6,
        //                custom_harga_7 + (custom_harga_7::float / 100::float * ?)::int as custom_harga_7,
        //                custom_harga_8 + (custom_harga_8::float / 100::float * ?)::int as custom_harga_8,
        //                custom_harga_9 + (custom_harga_9::float / 100::float * ?)::int as custom_harga_9,
        //                custom_harga_10 + (custom_harga_10::float / 100::float * ?)::int as custom_harga_10,
        //                id_harga_paket,id_destinasi,tanggal,quota,status,rate_hotel
        //                from paket_wisata_2_harga_paket where id_destinasi = ? and status=1 and tanggal::date > (now() + interval '5 days')::date order by tanggal asc",
        //                [$upPrice, $upPrice, $upPrice, $upPrice, $upPrice, $upPrice, $upPrice, $upPrice, $upPrice, $upPrice, $apiController->request->idDestination]);

        //        WITHOUT up_harga
        $priceList = $apiController->db->query(
            "select custom_harga_1 as custom_harga_1,
                custom_harga_2 as custom_harga_2,
                custom_harga_3 as custom_harga_3,
                custom_harga_4 as custom_harga_4,
                custom_harga_5 as custom_harga_5,
                custom_harga_6 as custom_harga_6,
                custom_harga_7 as custom_harga_7,
                custom_harga_8 as custom_harga_8,
                custom_harga_9 as custom_harga_9,
                custom_harga_10 as custom_harga_10,
                id_harga_paket,id_destinasi,tanggal,quota,status,rate_hotel
                from paket_wisata_2_harga_paket where id_destinasi = ? and status=1 and tanggal::date > (now() + interval '5 days')::date order by tanggal asc",
            [$apiController->request->idDestination]
        );

        $priceList->setFetchMode(Db::FETCH_OBJ);

        $arrayPrice = array();
        foreach ($price as $value) {
            array_push($arrayPrice, $value['custom_harga_1']); // 1 orang
            array_push($arrayPrice, $value['custom_harga_2']); // 2 orang
            array_push($arrayPrice, $value['custom_harga_3']); // 3 orang
            array_push($arrayPrice, $value['custom_harga_4']); // 4 orang
            array_push($arrayPrice, $value['custom_harga_5']); // 5 orang
            array_push($arrayPrice, $value['custom_harga_6']); // 6 orang
            array_push($arrayPrice, $value['custom_harga_7']); // 7 - 8
            array_push($arrayPrice, $value['custom_harga_8']); // 10 - 14
            array_push($arrayPrice, $value['custom_harga_9']); // 15 - 24
            array_push($arrayPrice, $value['custom_harga_10']); // 25 - 44
        }

        $arrayType = array();
        foreach ($typeWisata as $value) {
            array_push($arrayType, $value['nama_tipe_wisata']);
        }

        $arrayFacilities = array();
        foreach ($facilities as $value) {
            array_push($arrayFacilities, $value['nama_fasilitas']);
        }

        $arrayObject = array();
        foreach ($object as $value) {
            array_push($arrayObject, $value['nama_objek_wisata']);
        }

        $arrayFacilitiesExclude = array();
        foreach ($facilitiesExclude as $value) {
            array_push($arrayFacilitiesExclude, $value['nama_fasilitas']);
        }

        $tourType = (intval($detail['is_open_trip']) === 1) ? 'OPEN_TRIP' : 'SCHEDULE_TRIP';
        $priceUnit =  ($detail['mata_uang'] === 'IDR') ? 'Harga dalam rupiah (IDR) / pax' : 'Harga dalam ' . $detail['mata_uang'] . ' / pax';

        $listPrice = $priceList->fetchAll();

        //kebutuhan datepicker available
        $range = array();

        if (isset($apiController->request->year) && isset($apiController->request->month)) {
            $range = self::getDateAvailableWisata($apiController->request->year, $apiController->request->month);
        }
        //...

        $response = array(
            "destinationId" => $apiController->request->idDestination,
            "photos" => $photos,
            "title" => $detail['nama_destinasi'],
            "id_propinsi" => $detail['id_propinsi'],
            "location" => strval($detail['nama_propinsi']),
            "price" => (!min(array_filter($arrayPrice))) ? 0 : min(array_filter($arrayPrice)),
            "tourType" => $arrayType,
            "facilities" => $arrayFacilities,
            "tourObjects" => $arrayObject,
            "excludedFacilities" => $arrayFacilitiesExclude,
            "tourType" => $tourType,
            "currency" => ($detail['mata_uang'] === 'IDR') ? "Rp" : $detail['mata_uang'],
            "unit" => 'pax',
            "paxList" => self::getRangePax($listPrice),
            "listPrice" => self::getListPrice($listPrice[0]),
            "itenerary" => $iten,
            "iteneraryUrl" => "http://api.Travel.co.id/wisata/itenerary?id_destinasi=" . $apiController->request->idDestination,
            "priceUnit" => $priceUnit,
            "priceUp" => $upPrice,
            "prices" => $listPrice,
            "urlDetail" => "https://m.jadipergi.com/#!/wisata.html?id=" . $apiController->request->idDestination,
            "moreInformation" => 'Kami mengajak Anda mengunjungi monumen nasional atau yang populer disingkat dengan Monas atau Tugu Monas adalah monumen peringatan setinggi 132 meter yang didirikan untuk mengenang perlawanan dan perjuangan rakyat Indonesia.',
            "dateAvailability" => $range,
            "days" => $detail['days'],
            "nights" => $detail['nights'],
            "viewer" => $detail['viewer']
        );

        if (count($priceList) > 0) {
            $apiController->response->data = $response;

            // + Review

            $apiController->db->query('UPDATE ' . self::$table_paket_wisata_2_mt_destinasi . ' SET viewer = viewer + 1 WHERE id_destinasi = ? ', [$apiController->request->idDestination]);
        } else {
            $apiController->response->setStatus("01", "Detail is empty.");
        }
    }

    function construct_array_harga($data)
    {
        $arr_daftar_harga = array(
            array("harga" => $data['custom_harga_1'], "range_min" => "1", "range_max" => "1", "key" => "custom_harga_1"),
            array("harga" => $data['custom_harga_2'], "range_min" => "2", "range_max" => "2", "key" => "custom_harga_2"),
            array("harga" => $data['custom_harga_3'], "range_min" => "3", "range_max" => "3", "key" => "custom_harga_3"),
            array("harga" => $data['custom_harga_4'], "range_min" => "4", "range_max" => "4", "key" => "custom_harga_4"),
            array("harga" => $data['custom_harga_5'], "range_min" => "5", "range_max" => "5", "key" => "custom_harga_5"),
            array("harga" => $data['custom_harga_6'], "range_min" => "6", "range_max" => "6", "key" => "custom_harga_6"),
            array("harga" => $data['custom_harga_7'], "range_min" => "7", "range_max" => "8", "key" => "custom_harga_7"),
            array("harga" => $data['custom_harga_8'], "range_min" => "10", "range_max" => "14", "key" => "custom_harga_8"),
            array("harga" => $data['custom_harga_9'], "range_min" => "15", "range_max" => "24", "key" => "custom_harga_9"),
            array("harga" => $data['custom_harga_10'], "range_min" => "25", "range_max" => "44", "key" => "custom_harga_10")
        );
        $harga_by_tanggal = array(
            'tanggal' => $data['tanggal'],
            'harga' => $arr_daftar_harga
        );
        return $harga_by_tanggal;
    }

    private static function getListPrice($data)
    {
        $result = array();

        if (intval($data->custom_harga_1) !== 0) {
            $result[] = array("1", $data->custom_harga_1);
        }
        if (intval($data->custom_harga_2) !== 0) {
            $result[] = array("2", $data->custom_harga_2);
        }
        if (intval($data->custom_harga_3) !== 0) {
            $result[] = array("3", $data->custom_harga_3);
        }
        if (intval($data->custom_harga_4) !== 0) {
            $result[] = array("4", $data->custom_harga_4);
        }
        if (intval($data->custom_harga_5) !== 0) {
            $result[] = array("5", $data->custom_harga_5);
        }
        if (intval($data->custom_harga_6) !== 0) {
            $result[] = array("6", $data->custom_harga_6);
        }
        if (intval($data->custom_harga_7) !== 0) {
            $result[] = array("7-8", $data->custom_harga_7);
        }
        if (intval($data->custom_harga_8) !== 0) {
            $result[] = array("10-14", $data->custom_harga_8);
        }
        if (intval($data->custom_harga_9) !== 0) {
            $result[] = array("15-24", $data->custom_harga_9);
        }
        if (intval($data->custom_harga_10) !== 0) {
            $result[] = array("25-44", $data->custom_harga_10);
        }
        return $result;
    }

    private static function getRangePax($data)
    {
        $priceData = $data[0];

        $result = range(1, 44);

        if ($priceData->custom_harga_1 == 0 || $priceData->custom_harga_1 == "") {
            unset($result[0]);
        }
        if ($priceData->custom_harga_2 == 0 || $priceData->custom_harga_2 == "") {
            unset($result[1]);
        }
        if ($priceData->custom_harga_3 == 0 || $priceData->custom_harga_3 == "") {
            unset($result[2]);
            //            self::unsetRange($result,2,3);
        }
        if ($priceData->custom_harga_4 == 0 || $priceData->custom_harga_4 == "") {
            unset($result[3]);
            //            self::unsetRange($result,4,5);
        }
        if ($priceData->custom_harga_5 == 0 || $priceData->custom_harga_5 == "") {
            unset($result[4]);
            //            self::unsetRange($result,6,9);
        }
        if ($priceData->custom_harga_6 == 0 || $priceData->custom_harga_6 == "") {
            unset($result[5]);
            //            self::unsetRange($result,10,14);
        }
        if ($priceData->custom_harga_7 == 0 || $priceData->custom_harga_7 == "") {
            self::unsetRange($result, 6, 9);
            //            self::unsetRange($result,15,23);
        }
        if ($priceData->custom_harga_8 == 0 || $priceData->custom_harga_8 == "") {
            self::unsetRange($result, 10, 14);
            //            self::unsetRange($result,24,44);
        }
        if ($priceData->custom_harga_9 == 0 || $priceData->custom_harga_9 == "") {
            self::unsetRange($result, 15, 23);
            //            self::unsetRange($result,24,44);
        }
        if ($priceData->custom_harga_10 == 0 || $priceData->custom_harga_10 == "") {
            self::unsetRange($result, 24, 44);
            //            self::unsetRange($result,24,44);
        }

        $finalresult = array();

        foreach ($result as $value) {
            $finalresult[] = (string) $value;
        }

        return  $finalresult;
    }

    private static function unsetRange(&$result, $bottom, $top)
    {
        for ($idx = $bottom; $idx <= $top; $idx++) {
            unset($result[$idx]);
        }
    }

    private static function getDateAvailableWisata($year, $month)
    {
        $date = $year . '-' . $month . '-' . '01';

        $sunday = 7 - date('N', strtotime($date)) + 1;

        $saturday = 7 - date('N', strtotime($date));

        $last_day = date('t', strtotime($date));

        $days = array();

        for ($i = $sunday; $i <= $last_day; $i = $i + 7) {
            $days[] = $year . '-' . $month . '-' . $i;
        }

        for ($i = $saturday; $i <= $last_day; $i = $i + 7) {
            $days[] = $year . '-' . $month . '-' . $i;
        }

        return $days;
    }
}