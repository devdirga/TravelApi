<?php

namespace Travel\App;

use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Phalcon\Db;

class GenerateEtiketController extends Controller
{
    protected $invoking = "Generate Etiket App"; //Generate PDF

    public function indexAction()
    {

        error_log("ETICKET___REQUEST : " . $_SERVER["REQUEST_URI"]);

        $request = new Request();
        $id_transaksi = strval($request->get("id_transaksi"));
        if ($id_transaksi === "") {
            $id_transaksi = $request->get("transaction_id");
        }

        error_log("ETICKET___ID_TRANSAKSI : " . $id_transaksi);

        $transaksi = $this->db->query("select * from proses_transaksi where id_transaksi = ? UNION select * from transaksi where id_transaksi = ? UNION select * from transaksi_backup where transaksi_backup.id_transaksi = ?", [$id_transaksi, $id_transaksi, $id_transaksi]);
        $transaksi->setFetchMode(Db::FETCH_OBJ);


        $data = $transaksi->fetch();

        $pdk = $this->db->query("select group_produk_jp from fmss.ft_mt_produk where id_produk = ?", [$data->id_produk]);

        $pdk->setFetchMode(Db::FETCH_OBJ);

        $produk = $pdk->fetch();

        $source = "";
        if ($produk->group_produk_jp === 'PESAWAT') {
            $source = "http://10.0.0.2/pdfgen/fastflight_receiver.php?id_transaksi=" . $id_transaksi;
        } elseif ($produk->group_produk_jp === 'HOTEL') {
            $id_outlet = $request->get("id_outlet");

            $source = "http://10.0.0.2/pdfgen/fasthotel_receiver.php?id_transaksi=" . $id_transaksi . "&id_outlet=" . $id_outlet;
        } elseif ($produk->group_produk_jp === 'WISATA') {
            $source = "http://10.0.0.2/pdfgen/paket_wisata_receiver.php?id_transaksi=" . $id_transaksi;
        } elseif ($produk->group_produk_jp === 'KAPAL') {
            $source = "http://10.0.0.2/pdfgen/tiket_pelnip_receiver.php?id_transaksi=" . $data->bill_info1;
        } elseif ($produk->group_produk_jp === 'TRAVEL') {
            $id_outlet = $request->get("id_outlet");

            $source = "http://10.0.0.2/pdfgen/tuxtravel_receiver.php?id_transaksi=" . $id_transaksi . "&id_outlet=" . $id_outlet;;
        } elseif ($produk->group_produk_jp === 'KERETA') {
            $source = "http://10.0.0.2/pdfgen/tiket_kereta_receiver_b2b.php?id_transaksi=" . $id_transaksi;
        } elseif ($produk->group_produk_jp === 'RAILINK') {
            $source = "http://10.0.0.2/pdfgen/tiket_kereta_receiver_b2b_rail.php?id_transaksi=" . $id_transaksi;
        }

        error_log("ETICKET___GENERATE : " . $source);

        header("Content-type: application/pdf");

        header('Content-Disposition: attachment; filename="' . $id_transaksi . '.pdf"');
        $file = file_get_contents($source);

        echo $file;
    }
}