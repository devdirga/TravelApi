<?php

namespace Travel\Libraries\Parser\Wisata;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;
use Phalcon\Db;

class SearchResponseParser extends BaseResponseParser implements ResponseParser
{

    protected $message;
    protected $ambiguity = array('%yogya%', '%jogja%');
    protected $keySearch1;
    protected $keySearch2;
    protected $up_harga;
    protected $url_foto = "http://best.Travel.co.id/";
    protected $cdn_url_foto = "https://static.scash.bz/jadipergi/img/wisata/";

    public function into(APIController $apiController)
    {
        if (isset($apiController->request->keySearch)) {
            if (!in_array($apiController->request->keySearch, $this->ambiguity)) {
                $this->ambiguity[0] = "%" . $apiController->request->keySearch . "%";
                $this->ambiguity[1] = "%" . $apiController->request->keySearch . "%";
            }
        } else {
            if (!in_array($apiController->request->keyCity, $this->ambiguity)) {
                $this->ambiguity[0] = "%" . $apiController->request->keyCity . "%";
                $this->ambiguity[1] = "%" . $apiController->request->keyCity . "%";
            }
        }

        /* left join mt_kota mk on mk.id_propinsi = d.id_propinsi */
        /* mk.nama_kota */

        $add_where = "";

        if (isset($apiController->request->duration)) {
            if (intval($apiController->request->duration) > 0) {
                $add_where = " and d.days=" . intval($apiController->request->duration) . " ";
            }
        }

        if (isset($apiController->request->keySearch)) {
            $searches = $apiController->db->fetchAll(
                "SELECT d.id_destinasi,mp.nama_propinsi,pwfd.use_cdn,pwfd.path_foto,d.nama_destinasi, d.days, d.nights, '[' || array_to_string(array_agg(quote_literal(obj.nama_objek_wisata)),',') || ']' AS obyek_wisata, d.viewer  
                FROM paket_wisata_2_mt_destinasi d 
                LEFT JOIN paket_wisata_2_objek_wisata obj ON obj.id_destinasi = d.id_destinasi 
                LEFT JOIN mt_propinsi mp on mp.id_propinsi = d.id_propinsi 
                LEFT JOIN paket_wisata_2_foto_destinasi pwfd ON pwfd.id_destinasi = d.id_destinasi AND urutan = 1
                WHERE d.id_destinasi IN 
                ( 
                   SELECT id_destinasi FROM paket_wisata_2_mt_destinasi WHERE id_propinsi IN ( SELECT id_propinsi FROM mt_propinsi WHERE lower(nama_propinsi) LIKE lower(?) OR lower(nama_propinsi) LIKE lower(?))              
                   UNION SELECT id_destinasi FROM paket_wisata_2_mt_destinasi WHERE lower(negara) LIKE lower(?) OR lower(negara) LIKE lower(?)     
                   UNION SELECT id_destinasi FROM paket_wisata_2_data_wilayah WHERE lower(nama_wilayah) LIKE lower(?) OR lower(nama_wilayah) LIKE lower(?)
                   UNION SELECT DISTINCT id_destinasi FROM paket_wisata_2_data_tipe_wisata WHERE id_tipe_wisata IN ( SELECT id_tipe_wisata FROM paket_wisata_2_mt_tipe_wisata WHERE lower(nama_tipe_wisata) LIKE lower(?) OR lower(nama_tipe_wisata) LIKE lower(?))
                   UNION SELECT id_destinasi FROM paket_wisata_2_mt_destinasi WHERE lower(nama_destinasi) LIKE lower(?) OR lower(nama_destinasi) LIKE lower(?)
                   UNION SELECT id_destinasi FROM paket_wisata_2_objek_wisata WHERE lower(nama_objek_wisata) LIKE lower(?) OR lower(nama_objek_wisata) LIKE lower(?)
                )
                AND ((d.id_destinasi IN ( SELECT DISTINCT id_destinasi FROM paket_wisata_2_harga_paket WHERE EXTRACT( MONTH FROM tanggal ) = ? AND EXTRACT( YEAR FROM tanggal) = ? AND tanggal > NOW()::DATE AND status=1 )))
                AND d.is_verified_os=1 
                AND d.is_active=1 " . $add_where . "
                AND d.id_supplier IN ( SELECT id_supplier FROM paket_wisata_2_mt_supplier WHERE is_verified_by_os=1 ) 
                GROUP BY 1,2,3,4,5",
                Db::FETCH_OBJ,
                [$this->ambiguity[0], $this->ambiguity[1], $this->ambiguity[0], $this->ambiguity[1], $this->ambiguity[0], $this->ambiguity[1], $this->ambiguity[0], $this->ambiguity[1], $this->ambiguity[0], $this->ambiguity[1], $this->ambiguity[0], $this->ambiguity[1], $apiController->request->month, $apiController->request->year]
            );
        } else {
            $searches = $apiController->db->fetchAll(
                "SELECT d.id_destinasi,mp.nama_propinsi,pwfd.use_cdn,pwfd.path_foto,d.nama_destinasi, d.days, d.nights, '[' || array_to_string(array_agg(quote_literal(obj.nama_objek_wisata)),',') || ']' AS obyek_wisata, d.viewer  
                FROM paket_wisata_2_mt_destinasi d 
                LEFT JOIN paket_wisata_2_objek_wisata obj ON obj.id_destinasi = d.id_destinasi 
                LEFT JOIN mt_propinsi mp on mp.id_propinsi = d.id_propinsi 
                LEFT JOIN paket_wisata_2_foto_destinasi pwfd ON pwfd.id_destinasi = d.id_destinasi AND urutan = 1
                WHERE d.id_destinasi IN 
                ( 
                   SELECT id_destinasi FROM paket_wisata_2_mt_destinasi WHERE id_propinsi IN ( SELECT id_propinsi FROM mt_propinsi WHERE lower(nama_propinsi) LIKE lower(?) OR lower(nama_propinsi) LIKE lower(?))              
                   UNION SELECT id_destinasi FROM paket_wisata_2_mt_destinasi WHERE lower(negara) LIKE lower(?) OR lower(negara) LIKE lower(?)     
                   UNION SELECT id_destinasi FROM paket_wisata_2_data_wilayah WHERE lower(nama_wilayah) LIKE lower(?) OR lower(nama_wilayah) LIKE lower(?)
                   --UNION SELECT DISTINCT id_destinasi FROM paket_wisata_2_data_tipe_wisata WHERE id_tipe_wisata IN ( SELECT id_tipe_wisata FROM paket_wisata_2_mt_tipe_wisata WHERE lower(nama_tipe_wisata) LIKE lower(?) OR lower(nama_tipe_wisata) LIKE lower(?))
                   --UNION SELECT id_destinasi FROM paket_wisata_2_mt_destinasi WHERE lower(nama_destinasi) LIKE lower(?) OR lower(nama_destinasi) LIKE lower(?)
                   --UNION SELECT id_destinasi FROM paket_wisata_2_objek_wisata WHERE lower(nama_objek_wisata) LIKE lower(?) OR lower(nama_objek_wisata) LIKE lower(?)
                )
                AND ((d.id_destinasi IN ( SELECT DISTINCT id_destinasi FROM paket_wisata_2_harga_paket WHERE EXTRACT( MONTH FROM tanggal ) = ? AND EXTRACT( YEAR FROM tanggal) = ? AND tanggal > NOW()::DATE AND status=1 )))
                AND d.is_verified_os=1 
                AND d.is_active=1 " . $add_where . "
                AND d.id_supplier IN ( SELECT id_supplier FROM paket_wisata_2_mt_supplier WHERE is_verified_by_os=1 ) 
                GROUP BY 1,2,3,4,5",
                Db::FETCH_OBJ,
                [$this->ambiguity[0], $this->ambiguity[1], $this->ambiguity[0], $this->ambiguity[1], $this->ambiguity[0], $this->ambiguity[1], $apiController->request->month, $apiController->request->year]
            );
        }

        /*
         *  union select id_destinasi from paket_wisata_2_mt_destinasi where lower(nama_destinasi) like lower(?) or lower(nama_destinasi) like lower(?)   
            union select id_destinasi from paket_wisata_2_objek_wisata where lower(nama_objek_wisata) like lower(?) or lower(nama_objek_wisata) like lower(?)    
                    
         */

        $idDest = '';

        foreach ($searches as $a) {
            $idDest .= $a->id_destinasi . ',';
        }

        $idDestination = ($idDest === '') ? '1' : rtrim($idDest, ",");


        //        WITH up_harga
        //        $this->up_harga = $apiController->db->query("select nilai from global_setting where kunci = 'setting_up_harga_paket_wisata_jadipergi'")->fetch()['nilai'];
        //        $list_harga = $apiController->db->fetchAll("select id_destinasi, LEAST(
        //                NULLIF(MIN(custom_harga_1 + (custom_harga_1::float / 100::float * " . $this->up_harga . ")::int ),0),
        //                NULLIF(MIN(custom_harga_2 + (custom_harga_2::float / 100::float * " . $this->up_harga . ")::int ),0),
        //                NULLIF(MIN(custom_harga_3 + (custom_harga_3::float / 100::float * " . $this->up_harga . ")::int ),0),
        //                NULLIF(MIN(custom_harga_4 + (custom_harga_4::float / 100::float * " . $this->up_harga . ")::int ),0),
        //                NULLIF(MIN(custom_harga_5 + (custom_harga_5::float / 100::float * " . $this->up_harga . ")::int ),0),
        //                NULLIF(MIN(custom_harga_6 + (custom_harga_6::float / 100::float * " . $this->up_harga . ")::int ),0),
        //                NULLIF(MIN(custom_harga_7 + (custom_harga_7::float / 100::float * " . $this->up_harga . ")::int ),0),
        //                NULLIF(MIN(custom_harga_8 + (custom_harga_8::float / 100::float * " . $this->up_harga . ")::int ),0),
        //                NULLIF(MIN(custom_harga_9 + (custom_harga_9::float / 100::float * " . $this->up_harga . ")::int ),0),
        //                NULLIF(MIN(custom_harga_10 + (custom_harga_10::float / 100::float * " . $this->up_harga . ")::int),0)) as harga 
        //                from paket_wisata_2_harga_paket  where extract(month from tanggal) = ? and extract(year from tanggal) = ? 
        //                and tanggal > NOW()::date and status=1 and id_destinasi in ($idDestination) group by id_destinasi", Db::FETCH_OBJ, [$apiController->request->month, $apiController->request->year]);

        //        WITHOUT up_harga

        $list_harga = $apiController->db->fetchAll("select id_destinasi, LEAST(
                NULLIF(MIN(custom_harga_1),0), NULLIF(MIN(custom_harga_2),0), NULLIF(MIN(custom_harga_3),0),
                NULLIF(MIN(custom_harga_4),0), NULLIF(MIN(custom_harga_5),0), NULLIF(MIN(custom_harga_6),0),
                NULLIF(MIN(custom_harga_7),0), NULLIF(MIN(custom_harga_8),0), NULLIF(MIN(custom_harga_9),0),
                NULLIF(MIN(custom_harga_10),0)) as harga 
                from paket_wisata_2_harga_paket  where extract(month from tanggal) = ? and extract(year from tanggal) = ? 
                and tanggal > NOW()::date and status=1 and id_destinasi in ($idDestination) group by id_destinasi", Db::FETCH_OBJ, [$apiController->request->month, $apiController->request->year]);

        $arrHarga = array();

        foreach ($list_harga as $v) {
            $arrHarga[$v->id_destinasi] = $v->harga;
        }

        foreach ($searches as $value) {
            $apiController->response->data[] = (object) array(
                "id_destinasi" => $value->id_destinasi,
                "nama_destinasi" => $value->nama_destinasi,
                "nama_propinsi" => strval($value->nama_propinsi),
                "days" => $value->days,
                "nights" => $value->nights,
                "harga" => $arrHarga[$value->id_destinasi],
                "foto" => (intval($value->use_cdn) === 1 ? $this->cdn_url_foto . $value->path_foto : $this->url_foto . $value->path_foto),
                "obyek_wisata" => json_decode(str_replace("'", "\"", str_replace("''", "", $value->obyek_wisata))),
                "viewer" => $value->viewer
            );
        }

        if (count($apiController->response->data) <= 0) {
            $apiController->response->setStatus("01", "Tour is not found.");
        }
    }
}