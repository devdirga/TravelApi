<?php

namespace Travel\Libraries\Parser\Hotel;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\HttpConnect;

class DestinationResponseParser extends BaseResponseParser implements ResponseParser
{

    protected $message;

    public function into(APIController $apiController)
    {
        /* remark by jarvis
        $buff = $apiController->db->query("SELECT partner_central_id FROM mt_biller WHERE id_biller = ?", [167]);
        $buff->setFetchMode(Db::FETCH_OBJ);
        $buffer = $buff->fetch();
        $secretkey = $buffer->partner_central_id;
        $url_get_key = 'https://api.tiket.com:443/apiv1/payexpress?output=json&&method=getToken&secretkey=' . $secretkey;
	$token = json_decode(HttpConnect::sendToGETAPI($url_get_key))->token;
	*/

        $token = json_decode(HttpConnect::sendToSecureAPI("https://api.tiket.com:443/apiv1/payexpress?method=getToken&secretkey=f35af4b9e00caca86538ffc7ea16f291&output=json"))->token;
        $url_get_data = 'https://api.tiket.com/search/autocomplete/hotel?q=' . urlencode($apiController->request->keyword) . '&token=' . $token . '&output=json';

        foreach (json_decode(HttpConnect::sendToSecureAPI($url_get_data))->results->result as $result) {
            if ($result->country_id === 'id') {
                //                if(strpos($result->id, 'province') === false){
                $apiController->response->data[] = (object) array(
                    "weight" => $result->weight,
                    "distance" => $result->distance,
                    "skey" => $result->skey,
                    "country_id" => $result->country_id,
                    "label" => $result->label,
                    "label_location" => $result->label_location,
                    "count_location" => $result->count_location,
                    "category" => $result->category,
                    "tipe" => $result->tipe,
                    "business_uri" => $result->business_uri,
                    "key" => $result->id,
                    "value" => $result->label,
                    "extraKey" => $result->label_location,
                    "group" => property_exists($result, "category_label") ? $result->category_label : $result->category,
                    "count" => property_exists($result, "count_location") ? intval($result->count_location) : 1
                );
                //                }                
            }
        }

        //        $cities = $apiController->db->query("SELECT nama_kota as aditional_key, kode_kota_fp as node_key, nama_kota as node_value, 'KOTA' as groupby from hotel_data_kota_fp where kode_negara_fp = 'IDN' AND upper(nama_kota) like upper(?) order by nama_kota ASC", ["%" . $apiController->request->keyword . "%"])->fetchAll();
        //        $this->iterate($apiController->response->data, $cities);        
        //        $regions = $apiController->db->query("select kode_wilayah as node_key, nama_wilayah as node_value ,nama_kota as aditional_key,'WILAYAH' as groupby from hotel_data_detail_3 where upper(nama_wilayah) like upper(?) and nama_hotel<>'' group by 1,2,3 order by nama_wilayah ASC", ["%" . $apiController->request->keyword . "%"])->fetchAll();
        //        $this->iterate($apiController->response->data, $regions);
        //        $others = $apiController->db->query("select nama_kota as aditional_key, id_hotel as node_key, case when trim(nama_wilayah) <> '' then nama_hotel || ', ' || nama_wilayah || ', ' || nama_kota else nama_hotel || ', ' || nama_kota end as node_value,'NAMA HOTEL' as groupby from hotel_data_detail_3 where upper(nama_hotel || ', ' || nama_kota) like upper(?) and nama_hotel<>'' order by nama_hotel ASC", ["%" . $apiController->request->keyword . "%"])->fetchAll();
        //        $this->iterate($apiController->response->data, $others);
    }

    private function iterate(&$data, $items)
    {
        foreach ($items as $item) {
            $object = new \stdClass();
            $object->key = $item["node_key"];
            $object->value = rtrim(trim($item["node_value"]), ',');
            $object->extraKey = $item["aditional_key"];
            $object->group = $item["groupby"];
            $data[] = $object;
        }
    }
}