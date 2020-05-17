<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Travel\App;

/**
 * Description of GenerateEtiketCustomController
 *
 * @author bimasakti
 */

use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Travel\Libraries\ProductCode;
use Phalcon\Db;

class GenerateEtiketCustomController extends Controller
{
    protected $invoking = "Generate Etiket Custom App"; //Generate PDF

    //put your code here

    public function indexAction()
    {
        $request = new Request();

        $id_transaksi = $request->get("id_transaksi");
        $transaksi = $this->db->query("select * from transaksi where id_transaksi = ?", [$id_transaksi]);
        $transaksi->setFetchMode(Db::FETCH_OBJ);
        $data = $transaksi->fetch();
        $pdk = $this->db->query("select group_produk_jp from fmss.ft_mt_produk where id_produk = ?", [$data->id_produk]);
        $pdk->setFetchMode(Db::FETCH_OBJ);
        $produk = $pdk->fetch();
        if ($produk->group_produk_jp === 'PESAWAT') {
            $source = "http://10.0.0.2/pdfgen/fastflight_custom_receiver.php?id_transaksi=" . $id_transaksi . "&logo=" . $request->get("logo") . "&contact=" . $request->get("contact");
        } elseif ($produk->group_produk_jp === 'HOTEL') {
            $id_outlet = $request->get("id_outlet");
            $source = "http://10.0.0.2/pdfgen/fasthotel_custom_receiver.php?id_transaksi=" . $id_transaksi . "&id_outlet=" . $id_outlet . "&logo=" . $request->get("logo") . "&contact=" . $request->get("contact");
        } elseif ($produk->group_produk_jp === 'WISATA') {
            $source = "http://10.0.0.2/pdfgen/paket_wisata_custom_receiver.php?id_transaksi=" . $id_transaksi . "&logo=" . $request->get("logo") . "&contact=" . $request->get("contact");
        } elseif ($produk->group_produk_jp === 'KAPAL') {
            $source = "http://10.0.0.2/pdfgen/tiket_pelnip_custom_receiver.php?id_transaksi=" . $data->bill_info1 . "&logo=" . $request->get("logo") . "&contact=" . $request->get("contact");
        } elseif ($produk->group_produk_jp === 'TRAVEL') {
            $id_outlet = $request->get("id_outlet");
            $source = "http://10.0.0.2/pdfgen/tuxtravel_custom_receiver.php?id_transaksi=" . $id_transaksi . "&id_outlet=" . $id_outlet . "&logo=" . $request->get("logo") . "&contact=" . $request->get("contact");
        } elseif ($produk->group_produk_jp === 'KERETA') {
            $source = "http://10.0.0.2/pdfgen/tiket_kereta_custom_receiver_b2b.php?id_transaksi=" . $id_transaksi . "&logo=" . $request->get("logo") . "&contact=" . $request->get("contact");
        } elseif ($produk->group_produk_jp === 'RAILINK') {
            $source = "http://10.0.0.2/pdfgen/tiket_kereta_custom_receiver_b2b_rail.php?id_transaksi=" . $id_transaksi . "&logo=" . $request->get("logo") . "&contact=" . $request->get("contact");
        }

        header("Content-type: application/pdf");
        header('Content-Disposition: attachment; filename="' . $id_transaksi . '.pdf"');
        $file = file_get_contents($source);
        echo $file;
    }
}