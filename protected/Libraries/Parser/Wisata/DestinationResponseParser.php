<?php

namespace Travel\Libraries\Parser\Wisata;

use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\WisataMessage;
use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\APIController;
use Phalcon\Db;

class DestinationResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * TravelBus message response from core.
     * 
     * @var WisataMessage
     */
    protected $message;
    protected $ambiguity = array('%yogya%', '%jogja%');
    protected $keySearch1;
    protected $keySearch2;

    protected $url_foto = "http://best.Travel.co.id/";
    protected $bb_url_foto = "https://static.scash.bz/jadipergi/img/wisata/";

    public function into(APIController $apiController)
    {
        if (!in_array($apiController->request->keySearch, $this->ambiguity)) {
            $this->ambiguity[0] = "%" . $apiController->request->keySearch . "%";
            $this->ambiguity[1] = "%" . $apiController->request->keySearch . "%";
        }

        $dest = $apiController->db->query(
            "SELECT d.id_propinsi,mp.nama_propinsi,d.days, d.nights, d.nama_destinasi,d.id_destinasi, '[' || array_to_string(array_agg(quote_literal(obj.nama_objek_wisata)),',') || ']' as obyek_wisata 
                FROM paket_wisata_2_mt_destinasi d LEFT JOIN paket_wisata_2_objek_wisata obj on obj.id_destinasi = d.id_destinasi 
                LEFT JOIN mt_propinsi mp on mp.id_propinsi = d.id_propinsi 
                WHERE d.id_destinasi in (
                SELECT DISTINCT pwmd.id_destinasi FROM paket_wisata_2_mt_destinasi pwmd
                LEFT JOIN paket_wisata_2_harga_paket pwhp ON pwmd.id_destinasi = pwhp.id_destinasi 
                WHERE id_propinsi IN ( SELECT id_propinsi from mt_propinsi WHERE lower(nama_propinsi) LIKE lower(?) or lower(nama_propinsi) like lower(?) )  
                AND pwhp.tanggal::date >= NOW()::date             
                UNION SELECT distinct(pwmd.id_destinasi) FROM paket_wisata_2_mt_destinasi pwmd
                LEFT JOIN paket_wisata_2_harga_paket pwhp on pwmd.id_destinasi = pwhp.id_destinasi 
                WHERE lower(negara) like lower(?) AND pwhp.tanggal::date >= NOW()::date 
                UNION 
                SELECT DISTINCT pwdw.id_destinasi FROM paket_wisata_2_data_wilayah pwdw LEFT JOIN paket_wisata_2_harga_paket pwhp on pwdw.id_destinasi = pwhp.id_destinasi 
                WHERE (lower(nama_wilayah) LIKE lower(?) OR lower(nama_wilayah) LIKE lower(?)) AND pwhp.tanggal::date >= NOW()::date 
                UNION 
                SELECT DISTINCT pwdtw.id_destinasi FROM paket_wisata_2_data_tipe_wisata pwdtw LEFT JOIN paket_wisata_2_harga_paket pwhp on pwdtw.id_destinasi = pwhp.id_destinasi 
                WHERE id_tipe_wisata in (SELECT id_tipe_wisata FROM paket_wisata_2_mt_tipe_wisata WHERE lower(nama_tipe_wisata) LIKE lower(?) OR lower(nama_tipe_wisata) LIKE lower(?) ) AND pwhp.tanggal::date >= NOW()::date ) AND d.is_verified_os = 1 and d.is_active = 1 and d.id_supplier in( SELECT id_supplier FROM paket_wisata_2_mt_supplier where is_verified_by_os = 1) 
                GROUP BY 1,2,3,4,5,6",
            [
                $this->ambiguity[0], $this->ambiguity[1],
                $this->ambiguity[0],
                $this->ambiguity[0], $this->ambiguity[1],
                $this->ambiguity[0], $this->ambiguity[1]
            ]
        );

        /*


        SELECT d.id_propinsi,mp.nama_propinsi,d.days, d.nights, d.nama_destinasi,d.id_destinasi, '[' || array_to_string(array_agg(quote_literal(obj.nama_objek_wisata)),',') || ']' as obyek_wisata 
FROM paket_wisata_2_mt_destinasi d LEFT JOIN paket_wisata_2_objek_wisata obj on obj.id_destinasi = d.id_destinasi 
LEFT JOIN mt_propinsi mp on mp.id_propinsi = d.id_propinsi 
WHERE d.id_destinasi in (
SELECT DISTINCT pwmd.id_destinasi FROM paket_wisata_2_mt_destinasi pwmd
LEFT JOIN paket_wisata_2_harga_paket pwhp ON pwmd.id_destinasi = pwhp.id_destinasi 
WHERE id_propinsi IN ( SELECT id_propinsi from mt_propinsi WHERE lower(nama_propinsi) LIKE lower(?) or lower(nama_propinsi) like lower(?) )  
AND pwhp.tanggal::date >= NOW()::date             
UNION SELECT distinct(pwmd.id_destinasi) FROM paket_wisata_2_mt_destinasi pwmd
LEFT JOIN paket_wisata_2_harga_paket pwhp on pwmd.id_destinasi = pwhp.id_destinasi 
WHERE lower(negara) like lower(?) AND pwhp.tanggal::date >= NOW()::date 
UNION 
SELECT distinct pwmd.id_destinasi FROM paket_wisata_2_mt_destinasi pwmd LEFT JOIN paket_wisata_2_harga_paket pwhp on pwmd.id_destinasi = pwhp.id_destinasi 
WHERE (lower(nama_destinasi) like lower(?) OR lower(nama_destinasi) LIKE lower(?)) AND pwhp.tanggal::date >= NOW()::date 
UNION 
SELECT DISTINCT pwdw.id_destinasi FROM paket_wisata_2_data_wilayah pwdw LEFT JOIN paket_wisata_2_harga_paket pwhp on pwdw.id_destinasi = pwhp.id_destinasi 
WHERE (lower(nama_wilayah) LIKE lower(?) OR lower(nama_wilayah) LIKE lower(?)) AND pwhp.tanggal::date >= NOW()::date 
UNION 
SELECT DISTINCT pwow.id_destinasi FROM paket_wisata_2_objek_wisata pwow LEFT JOIN paket_wisata_2_harga_paket pwhp ON pwow.id_destinasi = pwhp.id_destinasi 
WHERE (lower(nama_objek_wisata) like lower(?) OR lower(nama_objek_wisata) like lower(?)) AND pwhp.tanggal::date >= NOW()::date 
UNION 
SELECT DISTINCT pwdtw.id_destinasi FROM paket_wisata_2_data_tipe_wisata pwdtw LEFT JOIN paket_wisata_2_harga_paket pwhp on pwdtw.id_destinasi = pwhp.id_destinasi 
WHERE id_tipe_wisata in (SELECT id_tipe_wisata FROM paket_wisata_2_mt_tipe_wisata WHERE lower(nama_tipe_wisata) LIKE lower(?) OR lower(nama_tipe_wisata) LIKE lower(?) ) AND pwhp.tanggal::date >= NOW()::date ) AND d.is_verified_os = 1 and d.is_active = 1 and d.id_supplier in( SELECT id_supplier FROM paket_wisata_2_mt_supplier where is_verified_by_os = 1) 
GROUP BY 1,2,3,4,5,6

         * 
         *          */

        $dest->setFetchMode(Db::FETCH_OBJ);

        $destination = $dest->fetchAll();

        $destinationResult = array();

        $index = 0;

        foreach ($destination as $value) {


            $fotos = $apiController->db->query("select * from paket_wisata_2_foto_destinasi where id_destinasi=? order by urutan asc", [$value->id_destinasi])->fetchAll();

            foreach ($fotos as $f) {
                switch (intval($f['use_cdn'])) {
                    case 1:
                        $url_photo =  $this->bb_url_foto . $f['path_foto'];
                        break;
                    case 0:
                        $url_photo =  $this->url_foto . $f['path_foto'];
                        break;
                    default:
                        $url_photo =  $this->url_foto . $f['path_foto'];
                        break;
                }
            }

            $node =  (object) array(
                "id_propinsi" => intval($value->id_propinsi),
                "nama_propinsi" => strval($value->nama_propinsi),
                "days" => $value->days,
                "nights" => $value->nights,
                "nama_destinasi" => $value->nama_destinasi,
                "id_destinasi" => $value->id_destinasi,
                "photo" => $url_photo,
                "obyek_wisata" => json_decode(str_replace("'", "\"", str_replace("''", "'", $value->obyek_wisata)))
            );

            $destinationResult[$index] = ($node);

            $index++;
        }

        if (count($destinationResult) > 0) {
            $apiController->response->data = $destinationResult;
        } else {
            $apiController->response->data = [];
            $apiController->response->setStatus("01", "Tujuan wisata dengan key " . $apiController->request->keySearch . " tidak ditemukan.");
        }
    }
}