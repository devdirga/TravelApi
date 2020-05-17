<?php

namespace Travel\Libraries\Parser;

use Travel\Libraries\Utility;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\BaseMessage;
use Phalcon\Db;


class BaselistResponseParser
{

    public $data_transaction;

    public $product;

    public $data_base;

    public $array_pesawat = array(176, 68, 175, 135, 105, 178, 92, 95, 183, 10022, 11016, 115, 187, 216, 177);

    public $array_hotel = array(136);

    public $array_kereta = array(118, 62);

    public $array_kapal = array(171);

    public $array_wisata = array(138);

    public $array_travel = array(123);

    /*
     * Parse message.
     * 
     * @return ResponseParser
     */
    public function parse(BaseMessage $message, APIController $apiController)
    {



        $this->message = $message;

        $transaksi = $apiController->db->query("SELECT * FROM transaksi WHERE id_transaksi = ?", [$apiController->request->transaction_id]);

        $transaksi->setFetchMode(Db::FETCH_OBJ);

        $this->product = $apiController->request->product;

        $this->data_transaction = $transaksi->fetch();

        if ($this->getName($this->data_transaction) === "") {
            $this->data_base = array();
        } else {
            $this->data_base = array(
                array("key" => "Komisi", "value" =>  "Rp " . number_format(Utility::getKomisi($apiController, $apiController->request->transaction_id), "2", ",", ".")),
                array("key" => "Nama", "value" => $this->getName($this->data_transaction)),
                array("key" => "Nomor HP", "value" => $this->getHP($this->data_transaction))
            );
        }

        return $this;
    }

    /*
     * Return instance parser.
     * 
     * @return ResponseParser
     */
    public static function instance()
    {
        return new static();
    }

    public function getName($data_transaksi)
    {
        if (in_array(intval($data_transaksi->id_biller), $this->array_pesawat)) {
            $tmp = explode(";", $data_transaksi->bill_info4);
            return $tmp[1] . " " . $tmp[2] . " " . $tmp[3];
        } else if (in_array(intval($data_transaksi->id_biller), $this->array_kereta)) {
            return str_replace(";", " ", $data_transaksi->bill_info4);
        } else if (in_array(intval($data_transaksi->id_biller), $this->array_hotel)) {
            return str_replace(";", " ", $data_transaksi->bill_info4);
        } else if (in_array(intval($data_transaksi->id_biller), $this->array_travel)) {
            return str_replace(";", " ", $data_transaksi->bill_info4);
        } else if (in_array(intval($data_transaksi->id_biller), $this->array_kapal)) {
            return str_replace(";", " ", $data_transaksi->bill_info4);
        } else {
            return str_replace(";", " ", $data_transaksi->bill_info4);
        }
    }

    public function getHP($data_transaksi)
    {

        if (in_array(intval($data_transaksi->id_biller), $this->array_pesawat)) {
            $data = explode(";", $data_transaksi->bill_info4);
            return str_replace("::", "", $data[7]);
        } else if (in_array(intval($data_transaksi->id_biller), $this->array_kereta)) {
            return $data_transaksi->bill_info22;
        } else if (in_array(intval($data_transaksi->id_biller), $this->array_hotel)) {
            return $data_transaksi->bill_info40;
        } else if (in_array(intval($data_transaksi->id_biller), $this->array_travel)) {
            return $data_transaksi->bill_info29;
        } else if (in_array(intval($data_transaksi->id_biller), $this->array_kapal)) {
            return $data_transaksi->bill_info56;
        } else  //wisata
        {
            return $data_transaksi->bill_info1;
        }
    }
}