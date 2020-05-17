<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\ProductCode;
use Phalcon\Db;

//use Travel\Libraries\Models\Outlet;

class GenerateAsterikStrukResponseParser extends BaseResponseParser implements ResponseParser
{

    /**
     * AppMessage.
     * 
     * @var AppMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {


        $trx = $apiController->db->query("SELECT * FROM transaksi WHERE id_transaksi = ?", [$apiController->request->id_transaksi]);

        $trx->setFetchMode(Db::FETCH_OBJ);

        $transaksi = $trx->fetch();

        $result = "";

        $pdk = $apiController->db->query("select group_produk_jp from fmss.ft_mt_produk where id_produk = ?", [$transaksi->id_produk]);

        $pdk->setFetchMode(Db::FETCH_OBJ);

        $produk = $pdk->fetch();


        if ($produk->group_produk_jp === 'PESAWAT') {
            $result = $this->getAsterikStrukPESAWAT($apiController, $transaksi);
        } elseif ($produk->group_produk_jp === 'KERETA') {
            $result = $this->getAsterikStrukKERETA($apiController, $transaksi);
        } elseif ($produk->group_produk_jp === 'HOTEL') {
            $result = $this->getAsterikStrukHOTEL($apiController, $transaksi);
        } elseif ($produk->group_produk_jp === 'KAPAL') {
            $result = $this->getAsterikStrukKAPAL($apiController, $transaksi);
        } elseif ($produk->group_produk_jp === 'WISATA') {
            $result = $this->getAsterikStrukWISATA($apiController, $transaksi);
        } elseif ($produk->group_produk_jp === 'TRAVEL') {
            $result = $this->getAsterikStrukTRAVEL($apiController, $transaksi);
        }

        $apiController->response->data = $result;
    }


    public function getAsterikStrukPESAWAT($apiController, $data)
    {

        $tanggal = $this->getFormatDate($this->getDateDeparture($data)) . " " . $this->getTimeDepart($data);

        $result = "";

        $result .= "CU";

        $result .= "*" . $data->id_produk;

        $result .= "*" . $this->getProductName($apiController, $data->id_produk);

        $result .= "*" . $tanggal;

        $result .= "*" . $this->getWebTicket($data->id_produk);

        $jenisStruk = 1;

        $flightStep = (intval($data->jenis_transaksi) === 0) ? "BOOKING" : "PAYMENT";

        if ($jenisStruk === 1) {
            if ($flightStep === "BOOKING") {
                $result .= "RESERVASI PESAWAT TERBANG * *";
                $result .= $this->field("NO. RESI") . " : ";
                $result .= $data->id_transaksi . " *";
                $result .= $this->field("TANGGAL") . " : " . $tanggal . " * *";
                $result .= $this->field("KODE BOOKING") . " : ";
                $result .= $this->getBookingCode($data) . " *";
                $result .= "#";
                $result .= $this->field("MASKAPAI") . " : ";
                $result .= $this->getProductName($apiController, $data->id_produk) . " *";
            } else {
                $result .= "STRUK PEMBAYARAN TIKET PESAWAT TERBANG * *";
                $result .= $this->field("NO. RESI") . " : ";
                $result .= $data->id_transaksi . " *";
                $result .= $this->field("TANGGAL") . " : " . $tanggal . " * *";
                $result .= $this->field("KODE BOOKING") . " : ";
                $result .= $this->getBookingCode($data) . " *";
                $result .= $this->field("MASKAPAI") . " : ";
                $result .= $this->getProductName($apiController, $data->id_produk) . " *";
            }

            $arrPax = explode("|", substr($this->getAdult7($data), 0, -1));

            $nBill = count($arrPax);
            $i = 1;
            $result .= $this->field("JML PENUMPANG") . " : ";
            $result .= $nBill . " *";
            $result .= $this->field("INFO BERANGKAT") . " ";
            $result .= " *";

            $result .= " *";

            if ($this->getIsTransitGo($data) == "1" || $this->getIsTransitGo($data) == "2") {
                $result .= " " . $this->field("RUTE") . "  : ";
                $result .= $this->getCityOrigin($data) . "-" . $this->getTransitViaGo($data) . " *";
            } else {
                $result .= " " . $this->field("RUTE") . "  : ";
                $result .= $this->getCityOrigin($data) . "-" . $this->getCityDestination($data) . " *";
            }

            $result .= "  " . $this->field("NO PESAWAT") . " : ";
            $result .= $this->getNoPenerbangan($this->getCodeFlightGo($data)) . " *";
            $result .= "  " . $this->field("TGL BRKT") . " : ";
            $result .= $this->getDateDeparture($data) . " *";
            $result .= "  " . $this->field("JAM BRKT (LT)") . " : ";
            $result .= $this->getTimeDepart($data) . " *";

            if (in_array(intval($this->getIsTransitGo($data)), array(1, 2))) {
                if (intval($this->getIsTransitGo($data)) === 1) {
                    $result .= " " . $this->field("RUTE") . "  : ";
                    $result .= $this->getTransitViaGo($data) . "-" . $this->getCityDestination($data) . " *";
                    $result .= "  " . $this->field("NO PESAWAT") . " : ";
                    $result .= $this->getNoPenerbangan($this->getCodeFlightTransitGo($data)) . " *";
                    $result .= "  " . $this->field("TGL BRKT") . " : ";
                    $result .= $this->getDateDeparture($data) . " *";
                    $result .= "  " . $this->field("JAM BRKT (LT)") . " : ";
                    $result .= $this->getTimeDepart2($data) . " *";
                }
                if (intval($this->getIsTransitGo($data)) === 2) {
                    $result .= " " . $this->field("RUTE") . "  : ";
                    $result .= $this->getTransitViaGo($data) . "-" . $this->getTransitViaGo2($data) . " *";
                    $result .= "  " . $this->field("NO PESAWAT") . " : ";
                    $result .= $this->getNoPenerbangan($this->getCodeFlightTransitGo($data)) . " *";
                    $result .= "  " . $this->field("TGL BRKT") . " : ";
                    $result .= $this->getDateDeparture($data) . " *";
                    $result .= "  " . $this->field("JAM BRKT (LT)") . " : ";
                    $result .= $this->getTimeDepart2($data) . " *";

                    $result .= " " . $this->field("RUTE") . "  : ";
                    $result .= $this->getTransitViaGo2($data) . "-" . $this->getCityDestination($data) . " *";
                    $result .= "  " . $this->field("NO PESAWAT") . " : ";
                    $result .= $this->getNoPenerbangan($this->getCodeFlightTransitGo($data)) . " *";
                    $result .= "  " . $this->field("TGL BRKT") . " : ";
                    $result .= $this->getDateDeparture($data) . " *";
                    $result .= "  " . $this->field("JAM BRKT (LT)") . " : ";
                    $result .= $this->getTimeDepart3() . " *";
                }
            }

            if ($this->getRute($data) == "1") {
                $result .= $this->field("INFO KEMBALI") . " ";
                $result .= " *";
                if ($this->getIsTransitBack($data) == "1" || $this->getIsTransitBack($data) == "2") {
                    $result .= " " . $this->field("RUTE") . "  : ";
                    $result .= $this->getCityDestination($data) . "-" . $this->getTransitViaBack($data) . " *";
                } else {
                    $result .= " " . $this->field("RUTE") . "  : ";
                    $result .= $this->getCityDestination($data) . "-" .  $this->getCityOrigin($data) . " *";
                }
                $result .= "  " . $this->field("NO PESAWAT") . " : ";
                $result .= $this->getNoPenerbangan($this->getCodeFlightBack($data)) . " *";
                $result .= "  " . $this->field("TGL KEMBALI") . " : ";
                $result .= $this->getDateArrival($data) . " *";
                $result .= "  " . $this->field("JAM KEMBALI (LT)") . ": ";
                $result .= $this->getTimeDepartReturn($data) . " *";
                if ($this->getIsTransitBack($data) == "1" || $this->getIsTransitBack($data) == "2") {
                    if ($this->getIsTransitBack($data) == "1") {
                        $result .= " " . $this->field("RUTE") . "  : ";
                        $result .= $this->getTransitViaBack($data) . "-" . $this->getCityOrigin($data) . " *";
                        $result .= "  " . $this->field("NO PESAWAT") . " : ";
                        $result .= $this->getNoPenerbangan($this->getCodeFlightTransitBack($data)) . " *";
                        $result .= "  " . $this->field("TGL KEMBALI") . " : ";
                        $result .= $this->getDateArrival($data) . " *";
                        $result .= "  " . $this->field("JAM KEMBALI (LT)") . ": ";
                        $result .= $this->getTimeDepartReturn2($data) . " *";
                    } else if ($this->getIsTransitBack($data) == "2") {
                        $result .= " " . $this->field("RUTE") . "  : ";
                        $result .= $this->getTransitViaBack($data) . "-" . $this->getTransitViaBack2($data) . " *";
                        $result .= "  " . $this->field("NO PESAWAT") . " : ";
                        $result .= $this->getNoPenerbangan($this->getCodeFlightTransitBack($data)) . " *";
                        $result .= "  " . $this->field("TGL KEMBALI") . " : ";
                        $result .= $this->getDateArrival($data) . " *";
                        $result .= "  " . $this->field("JAM KEMBALI (LT)") . ": ";
                        $result .= $this->getTimeDepartReturn2($data) . " *";

                        $result .= " " . $this->field("RUTE") . "  : ";
                        $result .= $this->getTransitViaBack2($data) . "-" . $this->getCityOrigin($data) . " *";
                        $result .= "  " . $this->field("NO PESAWAT") . " : ";
                        $result .= $this->getNoPenerbangan($this->getCodeFlightTransitBack2($data)) . " *";
                        $result .= "  " . $this->field("TGL KEMBALI") . " : ";
                        $result .= $this->getDateArrival($data) . " *";
                        $result .= "  " . $this->field("JAM KEMBALI (LT)") . ": ";
                        $result .= $this->getTimeDepartReturn3($data) . " *";
                    }
                }
            }


            $result .= $this->field("PENUMPANG") . " : *";
            $tipePax = array("ADT", "CHD", "INF");

            foreach ($arrPax as $key) {
                $details = explode(";", $key);
                $nama = $details[1];
                $title = $details[0];
                if ($title == "null" || $title == "" || $title == null) {
                    $title = "";
                }
                if (in_array($title, $tipePax)) {
                    $nama = $details[2] . " " . $details[3];
                    $title = $details[1];
                    $result .= $i . ". " . $title . " " . $nama . " *";
                } else {
                    $result .= $i . ". " . $title . " " . $nama . " *";
                }
                $i++;
            }
            $tot = $this->getNominal($data);
            $adminNominal = $this->getNominalAdmin($data);
            $totalAsuransi = "";
            $total = 0;
            if ($this->getIdPel3($data) == "1") {
                $totalAsuransi = $this->getClassname3($data);
                $total = intval($tot) + intval($adminNominal) + intval($totalAsuransi);
            } else {
                $total = intval($tot) + intval($adminNominal);
            }
            $result .= $this->field("NOMINAL TIKET") . " : ";
            $result .= "Rp " . $this->value($tot) . " *";
            $result .= $this->field("BOOKING FEE") . " : ";
            $result .= "Rp " . $this->value($adminNominal) . " *";
            if ($this->getIdPel3($data) == "1") {
                $result .= $this->field("NOMINAL ASURANSI") . ": ";
                $result .= "Rp " . $this->value($totalAsuransi) . " *";
            }
            $result .= $this->field(" ") . "   " . $this->garis() . "*";
            $result .= $this->field("TOTAL BAYAR") . " : ";
            $result .= "Rp " . $this->value($total) . " *";
            $result .= " *";
            $result .= $this->field("TERBILANG") . " : *";
            //$jenisStruk
            //$result .= $this->ww($this->terbilang($total) . "RUPIAH", $rs["jenis_struk"]) . "**";

            $result .= $this->ww($this->terbilang($total) . "RUPIAH", $jenisStruk) . "**";
        } else {

            $strukBerangkat = array();
            $strukBerangkat[0] = "" . $this->field("INFO BERANGKAT") . " ";
            if ($this->getIsTransitGo($data) == "1" || $this->getIsTransitGo($data) == "2") {
                $strukBerangkat[1] = " " . $this->field("RUTE") . "  : ";
                $strukBerangkat[1] .= $this->field($this->getCityOrigin($data) . "-" . $this->getTransitViaGo($data)) . " ";
            } else {
                $strukBerangkat[1] = " " . $this->field("RUTE") . "  : ";
                $strukBerangkat[1] .= $this->field($this->getCityOrigin($data) . "-" . $this->getCityDestination($data)) . " ";
            }
            $strukBerangkat[2] = "  " . $this->field("NO PESAWAT") . " : ";
            $strukBerangkat[2] .= $this->getNoPenerbangan($this->field($this->getCodeFlightGo($data))) . " ";
            $strukBerangkat[3] = "  " . $this->field("TGL BRKT") . " : ";
            $strukBerangkat[3] .= $this->field($this->getDateDeparture($data)) . " ";
            $strukBerangkat[4] = "  " . $this->field("JAM BRKT (LT)") . " : ";
            $strukBerangkat[4] .= $this->field($this->getTimeDepart($data)) . " ";
            if ($this->getIsTransitGo($data) == "1" || $this->getIsTransitGo($data) == "2") {
                if ($this->getIsTransitGo($data) == "1") {
                    $strukBerangkat[5] = " " . $this->field("RUTE") . "  : ";
                    $strukBerangkat[5] .= $this->field($this->getTransitViaGo($data) . "-" . $this->getCityDestination($data)) . " ";
                    $strukBerangkat[6] = "  " . $this->field("NO PESAWAT") . " : ";
                    $strukBerangkat[6] .= $this->getNoPenerbangan($this->field($this->getCodeFlightTransitGo($data))) . " ";
                    $strukBerangkat[7] = "  " . $this->field("TGL BRKT") . " : ";
                    $strukBerangkat[7] .= $this->field($this->getDateDeparture($data)) . " ";
                    $strukBerangkat[8] = "  " . $this->field("JAM BRKT (LT)") . " : ";
                    $strukBerangkat[8] .= $this->field($this->getTimeDepart2($data)) . " ";
                }
                if ($this->getIsTransitGo($data) == "2") {
                    $strukBerangkat[5] = " " . $this->field("RUTE") . "  : ";
                    $strukBerangkat[5] .= $this->field($this->getTransitViaGo($data) . "-" . $this->getTransitViaGo2($data)) . " ";
                    $strukBerangkat[6] = "  " . $this->field("NO PESAWAT") . " : ";
                    $strukBerangkat[6] .= $this->getNoPenerbangan($this->field($this->getCodeFlightTransitGo($data))) . " ";
                    $strukBerangkat[7] = "  " . $this->field("TGL BRKT") . " : ";
                    $strukBerangkat[7] .= $this->field($this->getDateDeparture($data)) . " ";
                    $strukBerangkat[8] = "  " . $this->field("JAM BRKT (LT)") . " : ";
                    $strukBerangkat[8] .= $this->field($this->getTimeDepart2($data)) . " ";

                    $strukBerangkat[9] = " " . $this->field("RUTE") . "  : ";
                    $strukBerangkat[9] .= $this->field($this->getTransitViaGo2($data) . "-" . $this->getCityDestination($data)) . " ";
                    $strukBerangkat[10] = "  " . $this->field("NO PESAWAT") . " : ";
                    $strukBerangkat[10] .= $this->getNoPenerbangan($this->field($this->getCodeFlightTransitGo2($data))) . " ";
                    $strukBerangkat[11] = "  " . $this->field("TGL BRKT") . " : ";
                    $strukBerangkat[11] .= $this->field($this->getDateDeparture($data)) . " ";
                    $strukBerangkat[12] = "  " . $this->field("JAM BRKT (LT)") . " : ";
                    $strukBerangkat[12] .= $this->field($this->getTimeDepart3($data)) . " ";
                }
            }
            $strukKembali = array();
            if ($this->getRute($data) == "1") {
                $strukKembali[0] = "    " . $this->field(" ") . $this->field("INFO KEMBALI") . " ";
                if ($this->getIsTransitBack($data) == "1" || $this->getIsTransitBack($data) == "2") {
                    $strukKembali[1] = " " . $this->field("RUTE") . "  : ";
                    $strukKembali[1] .= $this->field($this->getCityDestination($data) . "-" . $this->getTransitViaBack($data)) . " ";
                } else {
                    $strukKembali[1] = " " . $this->field("RUTE") . "  : ";
                    $strukKembali[1] .= $this->field($this->getCityDestination($data) . "-" . $this->getCityOrigin($data)) . " ";
                }
                $strukKembali[2] = "  " . $this->field("NO PESAWAT") . " : ";
                $strukKembali[2] .= $this->getNoPenerbangan($this->field($this->getCodeFlightGo($data))) . " ";
                $strukKembali[3] = "  " . $this->field("TGL KEMBALI") . " : ";
                $strukKembali[3] .= $this->field($this->getDateDeparture($data)) . " ";
                $strukKembali[4] = "  " . $this->field("JAM KEMBALI (LT)") . ": ";
                $strukKembali[4] .= $this->field($this->getTimeDepart($data)) . " ";
                if ($this->getIsTransitBack($data) == "1" || $this->getIsTransitBack($data) == "2") {
                    if ($this->getIsTransitBack($data) == "1") {
                        $strukKembali[5] = " " . $this->field("RUTE") . "  : ";
                        $strukKembali[5] .= $this->field($this->getTransitViaBack($data) . "-" . $this->getCityOrigin($data)) . " ";
                        $strukKembali[6] = "  " . $this->field("NO PESAWAT") . " : ";
                        $strukKembali[6] .= $this->getNoPenerbangan($this->field($this->getCodeFlightTransitBack($data))) . " ";
                        $strukKembali[7] = "  " . $this->field("TGL KEMBALI") . " : ";
                        $strukKembali[7] .= $this->field($this->getDateArrival($data)) . " ";
                        $strukKembali[8] = "  " . $this->field("JAM KEMBALI (LT)") . ": ";
                        $strukKembali[8] .= $this->field($this->getTimeDepartReturn2($data)) . " ";
                    }
                    if ($this->getIsTransitBack($data) == "2") {
                        $strukKembali[5] = " " . $this->field("RUTE") . "  : ";
                        $strukKembali[5] .= $this->field($this->getTransitViaBack($data) . "-" . $this->getTransitViaBack2($data)) . " ";
                        $strukKembali[6] = "  " . $this->field("NO PESAWAT") . " : ";
                        $strukKembali[6] .= $this->getNoPenerbangan($this->field($this->getCodeFlightTransitBack($data))) . " ";
                        $strukKembali[7] = "  " . $this->field("TGL KEMBALI") . " : ";
                        $strukKembali[7] .= $this->field($this->getDateArrival($data)) . " ";
                        $strukKembali[8] = "  " . $this->field("JAM KEMBALI (LT)") . ": ";
                        $strukKembali[8] .= $this->field($this->getTimeDepartReturn2($data)) . " ";

                        $strukKembali[9] = " " . $this->field("RUTE") . "  : ";
                        $strukKembali[9] .= $this->field($this->getTransitViaBack2($data) . "-" . $this->getCityOrigin($data)) . " ";
                        $strukKembali[10] = "  " . $this->field("NO PESAWAT") . " : ";
                        $strukKembali[10] .= $this->getNoPenerbangan($this->field($this->getCodeFlightTransitBack2($data))) . " ";
                        $strukKembali[11] = "  " . $this->field("TGL KEMBALI") . " : ";
                        $strukKembali[11] .= $this->field($this->getDateArrival($data)) . " ";
                        $strukKembali[12] = "  " . $this->field("JAM KEMBALI (LT)") . ": ";
                        $strukKembali[12] .= $this->field($this->getTimeDepartReturn3($data)) . " ";
                    }
                }
            }

            $arrPax = explode("|", substr($this->getAdult7($data), 0, -1));

            $nBill = count($arrPax);
            if ($this->getFlightStep($data) == "BOOKING") {
                $result .= $this->field(" ") . $this->field(" ") . "    " . "STRUK BOOKING TIKET PESAWAT TERBANG * *";
                $result .= $this->field("NO. RESI") . "      : ";
                $result .= $this->field($id) . " " . $this->field(" ") . "     #";
                $result .= $this->field("TANGGAL") . "      : " . $this->field($tgl) . " " . $this->field(" ") . $this->field("MASKAPAI") . "      : " . $this->field($maskapai) . " *";
                $result .= $this->field("KODE BOOKING") . "      : ";
                $result .= $this->field($this->getBookingCode($data)) . " " . $this->field(" ") . "     " . $this->field("JML PENUMPANG") . "      : " . $nBill . "  *";
            } else {
                $result .= $this->field(" ") . $this->field(" ") . "    " . "STRUK PEMBAYARAN TIKET PESAWAT TERBANG * *";
                $result .= $this->field("NO. RESI") . "      : ";
                $result .= $this->field($id) . " " . $this->field(" ") . "     *";
                $result .= $this->field("TANGGAL") . "      : " . $this->field($tgl) . " " . $this->field(" ") . $this->field("MASKAPAI") . "      : " . $this->field($maskapai) . " *";
                $result .= $this->field("KODE BOOKING") . "      : ";
                $result .= $this->field($this->getBookingCode($data)) . " " . $this->field(" ") . "     " . $this->field("JML PENUMPANG") . "      : " . $nBill . "  *";
            }

            $i = 1;
            $result .= " *";
            $result .= $strukBerangkat[0] . $this->field(" ") . "         " . $strukKembali[0] . " *";
            $result .= $strukBerangkat[1] . $this->field(" ") . "         " . $strukKembali[1] . " *";
            $result .= $strukBerangkat[2] . $this->field(" ") . "         " . $strukKembali[2] . " *";
            $result .= $strukBerangkat[3] . $this->field(" ") . "         " . $strukKembali[3] . " *";
            $result .= $strukBerangkat[4] . $this->field(" ") . "         " . $strukKembali[4] . " *";
            if ($this->getIsTransitGo($data) == "1" || $this->getIsTransitGo($data) == "2" || $this->getIsTransitBack($data) == "1" || $this->getIsTransitBack($data) == "2") {
                $result .= $strukBerangkat[5] . $this->field(" ") . "         " . $strukKembali[5] . " *";
                $result .= $strukBerangkat[6] . $this->field(" ") . "         " . $strukKembali[6] . " *";
                $result .= $strukBerangkat[7] . $this->field(" ") . "         " . $strukKembali[7] . " *";
                $result .= $strukBerangkat[8] . $this->field(" ") . "         " . $strukKembali[8] . " *";
                if ($this->getIsTransitBack($data) == "2" || $this->getIsTransitGo($data) == "2") {
                    $result .= $strukBerangkat[9] . $this->field(" ") . "         " . $strukKembali[9] . " *";
                    $result .= $strukBerangkat[10] . $this->field(" ") . "         " . $strukKembali[10] . " *";
                    $result .= $strukBerangkat[11] . $this->field(" ") . "         " . $strukKembali[11] . " *";
                    //file_put_contents("/var/www/test.json", print_r($strukKembali[12], true));
                    $result .= $strukBerangkat[12] . $this->field(" ") . "         " . $strukKembali[12] . " *";
                }
            }
            $result .= " *";

            $result .= $this->field("PENUMPANG") . "  *";
            $tipePax = array("ADT", "CHD", "INF");
            foreach ($arrPax as $key) {
                $details = explode(";", $key);
                $nama = $details[1];
                $title = $details[0];
                if ($title == "null" || $title == "" || $title == null) {
                    $title = "";
                }
                if (in_array($title, $tipePax)) {
                    $nama = $details[2] . " " . $details[3];
                    $title = $details[1];
                    $result .= $i . ". " . $title . " " . $nama . " *";
                } else {
                    $result .= $i . ". " . $title . " " . $nama . " *";
                }
                $i++;
            }
            $tot = $this->getNominal($data);
            $adminNominal = $this->getNominalAdmin($data);
            $totalAsuransi = "0";
            $total = 0;
            if ($this->getIdPel3($data) == "1") {
                $totalAsuransi = $this->getClassname3($data);
                $total = intval($tot) + intval($adminNominal) + intval($totalAsuransi);
            } else {
                $total = intval($tot) + intval($adminNominal);
            }
            $result .= "    " . $this->field(" ") . $this->field(" ") . $this->field(" ") . "          " . $this->field("NOMINAL TIKET") . " : ";
            $result .= "Rp " . $this->value($tot) . " *";
            $result .= "    " . $this->field(" ") . $this->field(" ") . $this->field(" ") . "          " . $this->field("BOOKING FEE") . " : ";
            $result .= "Rp " . $this->value($adminNominal) . " *";
            if ($this->getIdPel3($data) == "1") {
                $result .= "    " . $this->field(" ") . $this->field(" ") . $this->field(" ") . "          " . $this->field("NOMINAL ASURANSI") . ": ";
                $result .= "Rp " . $this->value($totalAsuransi) . " *";
            }
            $result .= "    " . $this->field(" ") . $this->field(" ") . $this->field(" ") . "          " . $this->field(" ") . "   " . garis() . "*";
            $result .= "    " . $this->field(" ") . $this->field(" ") . $this->field(" ") . "          " . $this->field("TOTAL BAYAR") . " : ";
            $result .= "Rp " . $this->value($total) . " *";
            $result .= " *";
        }

        return $result;
    }

    public function getAsterikStrukKERETA($apiController, $data)
    {

        $result = "";

        $jenisStruk = 1;

        $Transaksi_Status = (intval($data->jenis_transaksi) === 0) ? "BOOKING" : "PAYMENT";

        if ($jenisStruk == 1) {
            if ($Transaksi_Status === "BOOKING") {
                $result .= "STRUK BOOKING TIKET KERETA";
            } else {
                $result .= "STRUK PEMBAYARAN TIKET KERETA";
            }

            $result .= "*" . $data->id_produk . " * ";

            $result .= "*" . $this->getProductName($apiController, $data->id_produk) . " * ";

            $result .= $this->field("TANGGAL") . " : " . $data->bill_info13 . " * ";

            $result .= $this->field("NAMA") . " : " . $data->bill_info20 . " * ";

            $result .= $this->field("NO HP") . " : " . $data->bill_info22 . " * *";

            $result .= $this->field("KODE BOOK") . " : " . $data->bill_info2 . " * ";

            $result .= $this->field("NOMINAL") . " : " . $data->nominal . " * ";
        }


        return $result;
    }

    public function getAsterikStrukHOTEL($apiController, $data)
    {

        $result = "";

        $jenisStruk = 1;

        $Transaksi_Status = (intval($data->jenis_transaksi) === 0) ? "BOOKING" : "PAYMENT";

        if ($jenisStruk == 1) {
            if ($Transaksi_Status === "BOOKING") {
                $result .= "STRUK RESERVASI HOTEL";
            } else {
                $result .= "STRUK PEMBAYARAN HOTEL";
            }

            $result .= "*" . $data->id_produk . " * ";

            $result .= "*" . $this->getProductName($apiController, $data->id_produk) . " * ";

            $result .= $this->field("TANGGAL") . " : " . $data->bill_info33 . " * ";

            $result .= $this->field("NAMA") . " : " . $data->bill_info1 . " * ";

            $result .= $this->field("EMAIl") . " : " . $data->bill_info3 . " * *";

            $result .= $this->field("NOMINAL") . " : " . $data->nominal . " * ";
        }


        return $result;
    }

    public function getAsterikStrukKAPAL($apiController, $data)
    {

        $result = "";

        $jenisStruk = 1;

        $Transaksi_Status = (intval($data->jenis_transaksi) === 0) ? "BOOKING" : "PAYMENT";

        if ($jenisStruk == 1) {
            if ($Transaksi_Status === "BOOKING") {
                $result .= "STRUK BOOKING TIKET KAPAL";
            } else {
                $result .= "STRUK PEMBAYARAN TIKET KAPAL";
            }

            $result .= "*" . $data->id_produk . " * ";

            $result .= "*" . $this->getProductName($apiController, $data->id_produk) . " * ";

            $result .= $this->field("TANGGAL") . " : " . $data->bill_info24 . " * ";

            $result .= $this->field("NAMA") . " : " . $data->bill_info4 . " * ";

            $result .= $this->field("EMAIl") . " : " . $data->bill_info55 . " * *";

            $result .= $this->field("NOMINAL") . " : " . $data->nominal . " * ";
        }


        return $result;
    }

    public function getAsterikStrukTRAVEL($apiController, $data)
    {

        $result = "";

        $jenisStruk = 1;

        $Transaksi_Status = (intval($data->jenis_transaksi) === 0) ? "BOOKING" : "PAYMENT";

        if ($jenisStruk == 1) {
            if ($Transaksi_Status === "BOOKING") {
                $result .= "STRUK BOOKING TIKET TRAVEL";
            } else {
                $result .= "STRUK PEMBAYARAN TIKET TRAVEL";
            }

            $result .= "*" . $data->id_produk . " * ";

            $result .= "*" . $this->getProductName($apiController, $data->id_produk) . " * ";

            $result .= $this->field("TANGGAL") . " : " . $data->bill_info24 . " * ";

            $result .= $this->field("NAMA") . " : " . $data->bill_info2 . " * ";

            $result .= $this->field("EMAIl") . " : " . $data->bill_info3 . " * *";


            $result .= $this->field("KODE BOOK") . " : " . $data->bill_info5 . " * ";
            $result .= $this->field("NOMINAL") . " : " . $data->nominal . " * ";
        }

        return $result;
    }

    public function getAsterikStrukWISATA($apiController, $data)
    {

        $result = "";

        $jenisStruk = 1;

        $Transaksi_Status = (intval($data->jenis_transaksi) === 0) ? "BOOKING" : "PAYMENT";

        if ($jenisStruk == 1) {
            if ($Transaksi_Status === "BOOKING") {
                $result .= "STRUK RESERVASI PAKET WISATA";
            } else {
                $result .= "STRUK PEMBAYARAN PAKET WISATA";
            }

            $result .= "*" . $data->id_produk . " * ";

            $result .= "*" . $this->getProductName($apiController, $data->id_produk) . " * ";

            $result .= $this->field("TANGGAL") . " : " . $data->bill_info24 . " * ";

            $result .= $this->field("NAMA") . " : " . $data->bill_info2 . " * ";

            $result .= $this->field("HO HP") . " : " . $data->bill_info1 . " * *";

            $result .= $this->field("NOMINAL") . " : " . $data->nominal . " * ";
        }

        return $result;
    }

    public function getFormatDate($stringDate)
    {
        $timestamp = strtotime($stringDate);
        return strtoupper(date("d-F-Y", $timestamp));
    }

    public function getProductName($apiController, $idProduct)
    {
        $d = $apiController->db->query("select produk from mt_produk where id_produk = ? limit 1", [$idProduct]);
        $d->setFetchMode(Db::FETCH_OBJ);
        $dt = $d->fetch();
        return $dt->produk;
    }

    public function getWebTicket($idProduct)
    {
        $webTiket = "";

        switch ($idProduct) {
            case "TPSJ":
                $webTiket = "CALL CENTER SRIWIJAYA (021)29279777/08041777777";
                break;
            case "TPY6":
                $webTiket = "WEB MASKAPAI www.batavia-air.com/etiket/";
                break;
            case "TPJT":
                $webTiket = "WEB MASKAPAI www.lionair.co.id/";
                break;
            case "TPGA":
                $webTiket = "WEB MASKAPAI www.garuda-indonesia.com/id/";
                break;
            case "TPMZ":
                $webTiket = "WEB MASKAPAI www.merpati.co.id/";
                break;
            case "TPQZ":
                $webTiket = "WEB MASKAPAI checkin.airasia.com/chkin/step1.aspx?langId=15&lang=ID";
                break;
            case "TPQG":
                $webTiket = "WEB MASKAPAI book.citilink.co.id/RetrieveBooking.aspx";
                break;
            case "TPKP":
                $webTiket = "CALL CENTER KALSTAR AVIATION (021)29343456";
                break;
            case "TPSY":
                $webTiket = "WEB MASKAPAI www.sky-aviation.co.id/";
                break;
            case "TPRI":
                $webTiket = "WEB MASKAPAI booking.tigerairways.com/WebCheckIn.aspx";
                break;
            case "TPTN":
                $webTiket = "CALL CENTER TRIGANA AIR (021)34833942";
                break;
            case "TPXN":
                $webTiket = "CALL CENTER XPRESS AIR 500890";
                break;
            case "TPMV":
                $webTiket = "CALL CENTER TRANS NUSA (031)5047555";
                break;
            default:
                $webTiket = "CALL CENTER TRANS NUSA (031)5047555";
                break;
        }
        return $webTiket;
    }

    public function ww($txt, $jenis)
    {
        $len = ($jenis == 1 ? 50 : 100);
        return wordwrap($txt, $len, "*");
    }

    public function terbilang($number)
    {
        $bil = array("", "satu ", "dua ", "tiga ", "empat ", "lima ", "enam ", "tujuh ", "delapan ", "sembilan ", "sepuluh ", "sebelas ");
        $stringBuff = "";
        if ($number < 0) {
            $stringBuff .= "minus ";
            $stringBuff .= $this->terbilang($number * -1);
        }
        if ($number < 12 && $number > 0) {
            $stringBuff .= $bil[(int) $number];
        }
        if ($number >= 12 && $number < 20) {
            $stringBuff .= $this->terbilang($number - 10);
            $stringBuff .= "belas ";
        }
        if ($number >= 20 && $number < 100) {
            $stringBuff .= $this->terbilang($number / 10);
            $stringBuff .= "puluh ";
            $stringBuff .= $this->terbilang($number % 10);
        }
        if ($number >= 100 && $number < 200) {
            $stringBuff .= "seratus ";
            $stringBuff .= $this->terbilang($number % 100);
        }
        if ($number >= 200 && $number < 1000) {
            $stringBuff .= $this->terbilang($number / 100);
            $stringBuff .= "ratus ";
            $stringBuff .= $this->terbilang($number % 100);
        }
        if ($number >= 1000 && $number < 2000) {
            $stringBuff .= "seribu ";
            $stringBuff .= $this->terbilang($number % 1000);
        }
        if ($number >= 2000 && $number < 1000000) {
            $stringBuff .= $this->terbilang($number / 1000);
            $stringBuff .= "ribu ";
            $stringBuff .= $this->terbilang($number % 1000);
        }
        if ($number >= 1000000 && $number < 1000000000) {
            $stringBuff .= $this->terbilang($number / 1000000);
            $stringBuff .= "juta ";
            $stringBuff .= $this->terbilang($number % 1000000);
        }
        if ($number >= 1000000000 && $number < 1000000000000) {
            $stringBuff .= $this->terbilang($number / 1000000000);
            $stringBuff .= "milyar ";
            $stringBuff .= $this->terbilang(fmod($number, 1000000000));
        }
        if ($number >= 1000000000000) {
            $stringBuff .= $this->terbilang($number / 1000000000000);
            $stringBuff .= "trilyun ";
            $stringBuff .= $this->terbilang(fmod($number, 1000000000000));
        }
        if ($number == 0 && count($stringBuff) < 1) {
            $stringBuff .= "nol ";
        }
        return strtoupper($stringBuff);
    }

    public function field($str)
    {
        return sprintf("%-15s", $str);
    }

    function value($nilai, $minor = 0)
    {
        return sprintf("%15s", number_format($nilai, $minor, ",", "."));
    }

    public function getNoPenerbangan($no_penerbangan)
    {
        if (stristr($no_penerbangan, '(IW)') == TRUE) {
            return str_replace("(IW)", "(WINGS AIR)", $no_penerbangan);
        } elseif (stristr($no_penerbangan, '(ID)') == TRUE) {
            return str_replace("(ID)", "(BATIK AIR)", $no_penerbangan);
        } else {
            return $no_penerbangan;
        }
    }

    public function getNominalAdmin($data)
    {
        return $data->nominal_admin;
    }

    public function getNominal($data)
    {
        return $data->nominal;
    }

    public function getCodeFlightGo($data)
    {
        return $data->bill_info15;
    }

    public function getIsTransitGo($data)
    {
        return $data->bill_info65;
    }

    public function getIsTransitBack($data)
    {
        return $data->bill_info72;
    }

    public function getCityOrigin($data)
    {
        return $data->bill_info11;
    }

    public function getCityDestination($data)
    {
        return $data->bill_info12;
    }

    public function getBookingCode($data)
    {
        return $data->bill_info1;
    }

    public function getTransitViaGo($data)
    {
        return $data->bill_info68;
    }

    public function getTransitViaGo2($data)
    {
        return $data->bill_info73;
    }

    public function getDateDeparture($data)
    {
        return $data->bill_info13;
    }

    public function getTimeDepart($data)
    {
        return $data->bill_info14;
    }

    public function getIdPel3($data)
    {
        return $data->bill_info18; //ngawur
    }

    public function getRute($data)
    {
        return $data->bill_info21;
    }

    public function getTimeDepart3($data)
    {
        return $data->bill_info77;
    }

    public function getTransitViaBack($data)
    {
        return $data->bill_info76;
    }

    public function getCodeFlightTransitBack2($data)
    {
        return $data->bill_info75;
    }

    //mulai

    public function getCodeFlightBack($data)
    {
        return $data->bill_info64;
    }

    public function getDateArrival($data)
    {
        return $data->bill_info14;
    }

    public function getTimeDepartReturn($data)
    {
        return $data->bill_info28;
    }

    public function getCodeFlightTransitBack($data)
    {
        return $data->bill_info70;
    }

    public function getTimeDepartReturn2($data)
    {
        return $data->bill_info28;
    }

    public function getTimeDepartReturn3($data)
    {
        return $data->bill_info79;
    }

    public function getClassName3($data)
    {
        return $data->bill_info18; //ngawur
    }

    public function getTimeDepart2($data)
    {
        return $data->bill_info24;
    }

    public function getCodeFlightTransitGo($data)
    {
        return $data->bill_info69;
    }

    public function getCodeFlightTransitGo2($data)
    {
        return $data->bill_info74;
    }

    public function getAdult7($data)
    {
        return $data->bill_info34;
    }

    public function getFlightStep($data)
    {
        return "BOOKING";
    }

    public function garis()
    {
        return sprintf("%-'-18s", "-");
    }
}