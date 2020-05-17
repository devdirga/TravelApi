<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;
use Phalcon\Db;

class DataPaxResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Flight Message.
     * 
     * @var FlightMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $id_outlet      = $apiController->request->id_outlet;
        $id             = $apiController->request->id;
        $klausa         = "";

        $paxdata         = $apiController->request->paxdata; //if insert or not
        //$apiController->db->query("truncate ft_history_pax");die();
        if ($paxdata) { //insert new data
            $paxlist = json_decode($paxdata);
            foreach ($paxlist as $pax) {
                $produk         = $pax->produk;
                $id_outlet      = $pax->id_outlet;
                $email_pemesan  = $pax->email_pemesan;
                $hp_pemesan     = $pax->hp_pemesan;
                $nama           = $pax->pax_name;
                $firstname_pax  = $pax->firstname_pax;
                $lastname_pax   = $pax->lastname_pax;
                $email_pax      = $pax->pax_email;
                $hp_pax         = $pax->pax_hp;
                $tipe_identitas = $pax->pax_typeid;
                $no_identitas   = $pax->pax_id;
                $origin         = $pax->origin == "" ? "automatic" : $pax->origin;
                $tanggal_lahir  = (strlen($pax->pax_birthdate) == 10) ? $pax->pax_birthdate : "";
                if (empty($firstname_pax)) {
                    $parts = explode(' ', $nama);
                    $firstname_pax = array_shift($parts);
                    $lastname_pax = implode(" ", array_slice($parts, 0));
                }
                $is_exist = $apiController->db->fetchAll("SELECT * FROM ft_history_pax WHERE id_outlet = ? and lower(nama)=? and (no_identitas = ?)", Db::FETCH_OBJ, [$id_outlet, strtolower($nama), $no_identitas]);
                if (count($is_exist) == 0) {
                    if (strlen($tanggal_lahir) == 10) {
                        $apiController->db->query("INSERT INTO ft_history_pax(produk,id_outlet,email_pemesan,hp_pemesan,nama,email_pax,hp_pax,tipe_identitas,no_identitas,tanggal_lahir,is_active,nama_depan,nama_belakang,origin) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [$produk, $id_outlet, $email_pemesan, $hp_pemesan, $nama, $email_pax, $hp_pax, $tipe_identitas, $no_identitas, $tanggal_lahir, 1, $firstname_pax, $lastname_pax, $origin]);
                    } else {
                        $apiController->db->query("INSERT INTO ft_history_pax(produk,id_outlet,email_pemesan,hp_pemesan,nama,email_pax,hp_pax,tipe_identitas,no_identitas,is_active,nama_depan,nama_belakang,origin) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [$produk, $id_outlet, $email_pemesan, $hp_pemesan, $nama, $email_pax, $hp_pax, $tipe_identitas, $no_identitas, 1, $firstname_pax, $lastname_pax, $origin]);
                    }
                }
            }
        } else {
            if (empty($id)) {
                if (isset($nama))
                    $klausa = "and LOWER(nama) like '%" . strtolower($nama) . "%'";
                $data_pax = $apiController->db->fetchAll("SELECT * FROM ft_history_pax WHERE id_outlet = ? $klausa", Db::FETCH_OBJ, [$apiController->request->id_outlet]);
                if (count($data_pax) > 0) {
                    foreach ($data_pax as $item) {
                        $item->pax_name                  = $item->nama;
                        unset($item->nama);
                        $item->pax_email                 = $item->email_pax;
                        unset($item->email_pax);
                        $item->pax_hp                    = $item->hp_pax;
                        unset($item->hp_pax);
                        $item->pax_typeid                = $item->tipe_identitas;
                        unset($item->tipe_identitas);
                        $item->pax_id                    = $item->no_identitas;
                        unset($item->no_identitas);
                        $item->pax_birthdate             = $item->tanggal_lahir;
                        unset($item->tanggal_lahir);
                        $item->pax_firstname             = $item->nama_depan;
                        unset($item->nama_depan);
                        $item->pax_lastname              = $item->nama_belakang;
                        unset($item->nama_belakang);
                        $apiController->response->data[] = $item;
                    }
                } else {
                    $apiController->response->setStatus("01", "Data history pax is empty.");
                }
            } else { //hapus ID
                $apiController->db->query("DELETE FROM ft_history_pax where id=? and id_outlet=?", [$id, $id_outlet]);
            }
        }
    }
}