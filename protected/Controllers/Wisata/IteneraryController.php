<?php

namespace Travel\Wisata;

use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;

class IteneraryController extends Controller
{
    protected $invoking = "Itenerary Wisata App"; //Generate PDF

    public function indexAction()
    {

        $request = new Request();

        $id_destinasi = $request->get("id_destinasi");

        $source = "http://10.0.0.46/pdfgen/paket_wisata_receiver2.php?id_destinasi=" . $id_destinasi;

        header("Content-type: application/pdf");

        header('Content-Disposition: attachment; filename="' . $id_destinasi . '.pdf"');

        $file = file_get_contents($source);

        echo $file;
    }
}