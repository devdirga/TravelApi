<?php

namespace Travel\App;

use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Travel\Libraries\ProductCode;
use Phalcon\Db;
use Travel\Libraries\Models\Outlet;
use DateTime;
use DateTimeZone;

class GenerateStrukController extends Controller
{

    protected $invoking = "Generate Struk App"; // Generate Html

    public function indexAction()
    {
        $request = new Request();

        $id_transaksi = $request->get("id_transaksi");

        //$product = $this->db->query("select * from transaksi where id_transaksi = ?", [$id_transaksi]);
        $product = $this->db->query("select * from transaksi where id_transaksi = ? UNION select * from transaksi_backup where transaksi_backup.id_transaksi = ? ", [$id_transaksi, $id_transaksi]);

        $product->setFetchMode(Db::FETCH_OBJ);

        $data = $product->fetch();
        if ($data->id_produk != ProductCode::RAILINK) { // Railink skip pengambilan nama group
            $pdk = $this->db->query("select group_produk_jp from fmss.ft_mt_produk where id_produk = ?", [$data->id_produk]);
            $pdk->setFetchMode(Db::FETCH_OBJ);
            $data_pdk = $pdk->fetch();
        } else {
            $data_pdk->group_produk_jp = "RAIL";
        }
        $data->data_pdk->group_produk_jp = $data_pdk->group_produk_jp;

        switch ($data_pdk->group_produk_jp) {
            case ProductCode::PESAWAT:
                echo $this->getStrukPESAWAT($data, Outlet::take($data->id_outlet));
                break;
            case ProductCode::KERETA:
            case ProductCode::RAILINK:
                echo $this->getStrukKERETA($data, Outlet::take($data->id_outlet));
                break;
            case ProductCode::KAPAL:
                echo $this->getStrukKAPAL($data, Outlet::take($data->id_outlet));
                break;
            case ProductCode::TRAVEL:
                echo $this->getStrukTRAVEL($data, Outlet::take($data->id_outlet));
                break;
            case ProductCode::WISATA:
                echo $this->getStrukWISATA($data, Outlet::take($data->id_outlet));
                break;
            case ProductCode::HOTEL:
                echo $this->getStrukHOTEL($data, Outlet::take($data->id_outlet));
                break;
        }
    }

    public function getStrukPESAWAT($data, $outlet)
    {
        $isTransit = $data->bill_info65;
        $cityTransit = $data->bill_info68;
        $tgl_transit = $data->bill_info81;

        $time_arrive_back = $data->bill_info23;
        $time_depart_trans_back = $data->bill_info24;

        $maskapai = $data->bill_info10;
        $org = "";
        $org2 = "";
        $org3 = "";

        $dest = "";
        $dest2 = "";
        $dest3 = "";

        if (trim($data->bill_info24) == '') {
            $org = $this->get_city_name($data->bill_info11);
            $dest = $this->get_city_name($data->bill_info12);
        }

        if (trim($data->bill_info24) != '' && trim($data->bill_info26) == '') {
            $org = $this->get_city_name($data->bill_info11);
            $dest = $this->get_city_name($data->bill_info68);

            $org2 = $this->get_city_name($data->bill_info68);
            $dest2 = $this->get_city_name($data->bill_info12);
        }

        if (trim($data->bill_info26) != '') {
            $org = $this->get_city_name($data->bill_info11);
            $dest = $this->get_city_name($data->bill_info68);

            $org2 = $this->get_city_name($data->bill_info68);
            $dest2 = $this->get_city_name($data->bill_info73);

            $org3 = $this->get_city_name($data->bill_info73);
            $dest3 = $this->get_city_name($data->bill_info12);
        }

        $time_depart = $data->bill_info22;
        $time_depart2 = $data->bill_info24;
        $time_depart3 = $data->bill_info26;

        $time_arrive = $data->bill_info23;
        $time_arrive2 = $data->bill_info25;
        $time_arrive3 = $data->bill_info27;

        $date_depart = $data->bill_info13;
        $date_depart = strtoupper(date('d-M-Y', strtotime($date_depart)));
        if (intval($data->bill_info65) >= 1) {
            $date_depart2 = $this->get_real_date($data->bill_info11, $data->bill_info12, date('d-m-Y', strtotime($data->bill_info13)), $time_depart, $time_depart2);
        }
        if (intval($data->bill_info65) >= 2) {
            $date_depart3 = $data->bill_info14;
            $date_depart3 = strtoupper(date('d-M-Y', strtotime($date_depart3)));
        }

        $flight_num = "";
        $flight_num2 = "";
        $flight_num3 = "";

        if (stristr($data->bill_info15, '(IW)') == TRUE) {
            $flight_num = str_replace("(IW)", " (WINGS AIR)", $data->bill_info15);
            $flight_num2 = str_replace("(IW)", " (WINGS AIR)", $data->bill_info69);
            $flight_num3 = str_replace("(IW)", " (WINGS AIR)", $data->bill_info74);
        } elseif (stristr($data->bill_info15, '(ID)') == TRUE) {
            $flight_num = str_replace(" (ID)", " (BATIK AIR)", $data->bill_info15);
            $flight_num2 = str_replace(" (ID)", " (BATIK AIR)", $data->bill_info69);
            $flight_num3 = str_replace(" (ID)", " (BATIK AIR)", $data->bill_info74);
        } else {
            $flight_num = $data->bill_info15;
            $flight_num2 = $data->bill_info69;
            $flight_num3 = $data->bill_info74;
        }

        $booking_code = $data->bill_info1;

        $produk = $data->id_produk;

        $strPass = "";

        // ADULT PAX
        $passenger  =  $data->bill_info34;
        $passenger .= ($data->bill_info37 != '') ? "|" . $data->bill_info37 : "";
        $passenger .= ($data->bill_info40 != '') ? "|" . $data->bill_info40 : "";
        $passenger .= ($data->bill_info43 != '') ? "|" . $data->bill_info43 : "";
        $passenger .= ($data->bill_info46 != '') ? "|" . $data->bill_info46 : "";
        $passenger .= ($data->bill_info49 != '') ? "|" . $data->bill_info49 : "";
        $passenger .= ($data->bill_info52 != '') ? "|" . $data->bill_info52 : "";

        // CHILD PAX
        $passenger .= ($data->bill_info35 != '') ? "|" . $data->bill_info35 : "";
        $passenger .= ($data->bill_info38 != '') ? "|" . $data->bill_info38 : "";
        $passenger .= ($data->bill_info41 != '') ? "|" . $data->bill_info41 : "";
        $passenger .= ($data->bill_info44 != '') ? "|" . $data->bill_info44 : "";
        $passenger .= ($data->bill_info47 != '') ? "|" . $data->bill_info47 : "";
        $passenger .= ($data->bill_info50 != '') ? "|" . $data->bill_info50 : "";
        $passenger .= ($data->bill_info53 != '') ? "|" . $data->bill_info53 : "";

        // INFANT PAX
        $passenger .= ($data->bill_info36 != '') ? "|" . $data->bill_info36 : "";
        $passenger .= ($data->bill_info39 != '') ? "|" . $data->bill_info39 : "";
        $passenger .= ($data->bill_info42 != '') ? "|" . $data->bill_info42 : "";
        $passenger .= ($data->bill_info45 != '') ? "|" . $data->bill_info45 : "";
        $passenger .= ($data->bill_info48 != '') ? "|" . $data->bill_info48 : "";
        $passenger .= ($data->bill_info51 != '') ? "|" . $data->bill_info51 : "";
        $passenger .= ($data->bill_info54 != '') ? "|" . $data->bill_info54 : "";

        $arrPassenger = explode("|", $passenger);
        $i = 1;


        foreach ($arrPassenger as $pass) {
            if (trim($pass) == "") {
                break;
            }
            $penumpang = explode("::", $pass);
            $nama_penumpangs = explode(";", $penumpang[0]);
            array_shift($nama_penumpangs);
            $nama_penumpang = implode(" ", $nama_penumpangs);
            //$strPass .= '<font size="2">' . $i . '. ' . $nama_penumpang . '</font><br>';
            //$strPass .= $i . '. ' . $nama_penumpang;
            $strPass .= $i . '. ' . $nama_penumpangs[0] . ". " . $nama_penumpangs[1] . " " . $nama_penumpangs[2] . "*";

            $i++;
        }
        $img = "";
        $notes = "";
        switch ($produk) {
            case "TPJT":
                $url_maskapai = "http&#58;//www.lionair.co.id/";
                $maskapai_name = "LION AIR";
                $note6 = "6. Baggage allowance: 15KG for Lion Air and 10KG for Wings Air<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPQZ":
                $url_maskapai = "http&#58;//checkin.airasia.com/chkin/step1.aspx?langId=15&lang=ID";
                $maskapai_name = "AirAsia";
                $note6 = " 6. Baggage allowance: 7KG for " . $maskapai_name . "<br>";
                $note7 = "7. For AirAsia passengers, we recommend to checkin by " . $maskapai_name . " Web Checkin (<span style=\"text-transform:lowercase;\"><a href=\"http://checkin.airasia.com/chkin/step1.aspx?langId=1&lang=EN\" target=\"_blank\">http://checkin.airasia.com/chkin/step1.aspx?langId=1&lang=EN</a></span>) max 4 hours before departure time or will be subject to additional charge IDR 30,000.00 on airport checkin.<br>
             8. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPQG":
                $url_maskapai = "http&#58;//book.citilink.co.id/RetrieveBooking.aspx";
                $maskapai_name = "CITILINK";
                $note6 = "6. Baggage allowance: Cabin Baggage 7KG and Check-in Baggage 15KG for " . $maskapai_name . "<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPGA":
                $url_maskapai = "http&#58;//www.garuda-indonesia.com/id/";
                $maskapai_name = "GARUDA INDONESIA";
                $note6 = "6. Baggage allowance: Cabin Baggage 7KG and Check-in Baggage 20KG for " . $maskapai_name . "<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPKP":
                $url_maskapai = "http&#58;//www.kalstaronline.com/";
                $maskapai_name = "KALSTAR AVIATION";
                $note6 = "6. Baggage allowance: Cabin Baggage 7KG and Check-in Baggage 20KG for B737 and 5KG and 10KG for ATR42<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPRI":
                $url_maskapai = "http&#58;//booking.tigerairways.com/WebCheckIn.aspx";
                $maskapai_name = "MANDALA AIR";
                $note6 = "6. Baggage allowance: 10KG for " . $maskapai_name . "<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPMZ":
                $url_maskapai = "http&#58;//www.merpati.co.id/";
                $maskapai_name = "MERPATI AIRLINES";
                $note6 = "6. Baggage allowance: Business Class 30KG, Premium Class 25KG, Economy 20KG, Propeller Flight 10-15KG for MERPATI AIRLINES<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPSJ":
                $url_maskapai = "http&#58;//www.sriwijayaair.co.id/id";
                $maskapai_name = "SRIWIJAYA AIR";
                $note6 = "6. Baggage allowance: Cabin Baggage 7KG and Check-in Baggage 20KG for " . $maskapai_name . "<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPXN":
                $url_maskapai = "http&#58;//www.expressair.biz/";
                $maskapai_name = "XPRESS AIR";
                $note6 = "6. Baggage allowance: Cabin Baggage 7KG and Check-in Baggage 20KG for " . $maskapai_name . "<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPSY":
                $url_maskapai = "http&#58;//reservation.sky-aviation.co.id";
                $maskapai_name = "SKY AVIATION";
                $note6 = "6. Baggage allowance: Cabin Baggage 7KG and Check-in Baggage 20KG for " . $maskapai_name . "<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPMV":
                $url_maskapai = "http&#58;//www.transnusa.co.id/";
                $maskapai_name = "TRANS NUSA";
                $note6 = "6. Baggage allowance: Cabin Baggage 7KG and Check-in Baggage 20KG for " . $maskapai_name . "<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            case "TPTN":
                $url_maskapai = "http&#58;//www.trigana-air.com/";
                $maskapai_name = "TRIGANA AIR";
                $note6 = "6. Baggage allowance: Cabin Baggage 7KG and Check-in Baggage 20KG for " . $maskapai_name . "<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by " . $maskapai_name . "<br>";
                break;
            default:
                $url_maskapai = "";
                $maskapai_name = "";
                $note6 = "6. Baggage allowance: Cabin Baggage 7KG and Check-in Baggage 20KG<br>";
                $note7 = "7. Passengers agree with Terms and Conditions of carriage outlined by Airline <br>";
                break;
        }
        $notes = "<font size='1'>
            1. Your Airline e-ticket is electronically stored in airlines system and is subject to condition of contract<br>
            2. Please bring this electronic ticket receipt and your identity card on your travel in case required by airport/check-in counter.<br>
            3. Please arrive at the airport 120 minutes (2 hours) before the flight for domestic travel<br>
            4. Check-in closes 45 minutes before departure time<br>
            5. Please be at the gate 30 minutes before departure time or will leave you without you to avoid unnecessary delays<br>
            " . $note6 . $note7;

        $nominalbayar = intval($data->nominal) + intval($data->nominal_admin);
        //        $countPax = intval($data->bill_info18) + intval($data->bill_info19) + intval($data->bill_info20);

        return $this->alignLeft('CU') . '*' .
            $this->alignLeft('STRUK PEMBAYARAN TIKET PESAWAT TERBANG') . '**' .
            $this->alignLeft('NO. RESI') . ':' . $data->id_transaksi . '*' .
            $this->alignLeft('TANGGAL') . ': ' .
            $date_depart . ' ' . $time_depart . '**' .
            $this->alignLeft('KODE BOOKING') . ': ' . $booking_code . '*' .
            $this->alignLeft('MASKAPAI') . ': ' . $this->getNamaMaskapai($produk) . '*' .
            $this->alignLeft('JML PENUMPANG') . ': ' .  count($arrPassenger) .
            '**INFO BERANGKAT*' .
            $this->alignLeft('RUTE') . ': ' . $org . '-' . $dest . '*' .
            $this->alignLeft('NO PESAWAT') . ': ' . $flight_num . '*' .
            $this->alignLeft('TGL BRKT') . ': ' .  $date_depart . '*' .
            $this->alignLeft('JAM BRKT (LT)') . ': ' . $time_depart . '*' .
            $this->alignLeft('PENUMPANG') . ':*' .  $strPass . '*' . '*' .
            //$this->alignLeft('NOMINAL TIKET').': Rp ' . number_format($data->nominal, "0", ",", ".") . '*'.
            //$this->alignLeft('BOOKING FEE').': Rp 0*------------------*'.
            $this->alignLeft('TOTAL BAYAR') . ': Rp ' . number_format($nominalbayar, "0", ",", ".") . '*' .
            $this->alignLeft('TERBILANG') . ':*' . $this->ww($this->terbilang($nominalbayar), 1) .
            ' RUPIAH**STRUK PEMBAYARAN TIKET PESAWAT TERBANG YANG SAH*HARAP MELAKUKAN CHECK IN 2 JAM SEBELUM KEBERANGKATAN*CEK TIKET VIA MASKAPAI ' . $url_maskapai . '*' .
            $this->alignLeft('ID OUTLET') . ': ' . $outlet->idOutlet . '*';
    }
    public function get_real_date($kota_asal, $kota_tujuan, $tanggal_berangkat_asal, $jam_berangkat_asal, $jam_berangkat_tujuan)
    {

        $jam_berangkat_asal = $jam_berangkat_asal . ":00";
        $jam_berangkat_tujuan = $jam_berangkat_tujuan . ":00";

        $waktu_berangkat_asal = $tanggal_berangkat_asal . ' ' . $jam_berangkat_asal;
        $waktu_berangkat_tujuan = $tanggal_berangkat_asal . ' ' . $jam_berangkat_tujuan;

        $waktu_berangkat_asal_dt_tmp = DateTime::createFromFormat("d-m-Y H:i:s", $waktu_berangkat_asal, new DateTimeZone($this->get_time_from_city_timezone($kota_asal)));
        $waktu_berangkat_asal_dt = DateTime::createFromFormat("d-m-Y H:i:s", $waktu_berangkat_asal, new DateTimeZone($this->get_time_from_city_timezone($kota_asal)));
        $waktu_berangkat_asal_dt->setTimeZone(new DateTimeZone("Asia/Jakarta")); // digeser ke WIB
        $waktu_berangkat_asal_dt_gmt = gmdate('Y-m-d H:i:sO', $waktu_berangkat_asal_dt->getTimestamp());

        $waktu_berangkat_tujuan_dt = DateTime::createFromFormat("d-m-Y H:i:s", $waktu_berangkat_tujuan, new DateTimeZone($this->get_time_from_city_timezone($kota_tujuan)));
        $waktu_berangkat_tujuan_dt->setTimeZone(new DateTimeZone("Asia/Jakarta")); // digeser ke WIB
        $waktu_berangkat_tujuan_dt_gmt = gmdate('Y-m-d H:i:sO', $waktu_berangkat_tujuan_dt->getTimestamp());

        if (strtotime($waktu_berangkat_asal_dt_gmt) > strtotime($waktu_berangkat_tujuan_dt_gmt)) {
            $waktu_berangkat_asal_dt_tmp->modify("+24 hours");
        }
        return $waktu_berangkat_asal_dt_tmp->format("d-M-Y");
    }
    function get_time_from_city_timezone($city)
    {
        $timeval = 0;
        $jsonval = '{"ABU":"8.00","AMD":"5.50","AMQ":"9.00","AMS":"1.00","AOR":"8.00","ARD":"8.00","ARN":"1.00","ATH":"2.00","ATQ":"5.50","AUH":"4.00","BAH":"3.00","BCN":"1.00","BDJ":"8.00","BDO":"7.00","BEJ":"8.00","BIK":"9.00","BIL":"9.00","BJW":"8.00","BKI":"8.00","BKK":"7.00","BKS":"7.00","BLR":"5.50","BMU":"8.00","BOM":"5.50","BPN":"8.00","BRU":"1.00","BTH":"7.00","BTJ":"7.00","BTU":"8.00","BUW":"8.00","BWN":"8.00","BWX":"7.00","CAI":"2.00","CAN":"8.00","CCU":"5.50","CDG":"1.00","CEB":"8.00","CEI":"7.00","CGK":"7.00","CGP":"6.00","CGY":"8.00","CKG":"8.00","CMB":"5.50","CNX":"7.00","COK":"5.50","CPH":"1.00","CRK":"8.00","CSX":"8.00","CTS":"9.00","CTU":"8.00","DAC":"6.00","DAD":"7.00","DEL":"5.50","DIL":"9.00","DJB":"7.00","DJJ":"9.00","DME":"3.00","DMK":"7.00","DPS":"8.00","DRW":"9.50","DTB":"7.00","DUB":"0.00","DUM":"7.00","DUS":"1.00","DVO":"8.00","DXB":"4.00","ENE":"8.00","EWE":"9.00","FCO":"1.00","FKQ":"9.00","FLZ":"7.00","FRA":"1.00","FUK":"9.00","GAU":"5.50","GLX":"9.00","GNS":"7.00","GOI":"5.50","GTO":"8.00","HAN":"7.00","HDY":"7.00","HGH":"8.00","HHQ":"7.00","HKG":"8.00","HKT":"7.00","HLP":"7.00","HND":"9.00","HYD":"5.50","ICN":"9.00","IMF":"5.50","IPH":"8.00","IST":"2.00","ITM":"9.00","JAI":"5.50","JBB":"7.00","JED":"3.00","JFK":"-5.00","JHB":"8.00","JOG":"7.00","KAZ":"9.00","KBR":"8.00","KBU":"8.00","KBV":"7.00","KCH":"8.00","KDI":"8.00","KEP":"9.00","KHH":"8.00","KHN":"8.00","KIX":"9.00","KKC":"7.00","KLO":"8.00","KMG":"8.00","KMQ":"9.00","KNG":"9.00","KNO":"7.00","KNY":"9.00","KOE":"8.00","KOP":"7.00","KOX":"9.00","KTE":"8.00","KTG":"7.00","KTM":"5.75","KUA":"8.00","KUL":"8.00","KWE":"8.00","KWL":"8.00","LAH":"9.00","LAX":"-8.00","LBJ":"8.00","LBU":"8.00","LGK":"8.00","LGW":"0.00","LHR":"0.00","LKA":"8.00","LLG":"7.00","LOE":"7.00","LOP":"8.00","LPT":"7.00","LSW":"7.00","LUV":"9.00","LUW":"8.00","LWE":"8.00","MAA":"5.50","MAD":"1.00","MAN":"0.00","MCT":"4.00","MDC":"8.00","MDL":"6.50","MEL":"11.00","MEQ":"7.00","MFM":"8.00","MJU":"8.00","MKQ":"9.00","MKW":"9.00","MKZ":"8.00","MLE":"5.00","MLG":"7.00","MLK":"8.00","MLN":"8.00","MNA":"8.00","MNL":"8.00","MOF":"8.00","MUC":"1.00","MWK":"7.00","MXP":"1.00","MYY":"8.00","NAH":"8.00","NAW":"7.00","NBO":"3.00","NBX":"9.00","NKG":"8.00","NKM":"9.00","NNG":"8.00","NNT":"7.00","NNX":"8.00","NPO":"7.00","NRT":"9.00","NST":"7.00","NTX":"7.00","NYT":"6.50","ORD":"-6.00","ORY":"1.00","OSL":"1.00","OTI":"9.00","PDG":"7.00","PEK":"8.00","PEN":"8.00","PER":"8.00","PGK":"7.00","PHS":"7.00","PKN":"7.00","PKU":"7.00","PKY":"7.00","PLM":"7.00","PLW":"8.00","PNH":"7.00","PNK":"7.00","PNQ":"5.50","PPS":"8.00","PSJ":"8.00","PSU":"7.00","PTW":"9.00","PUM":"8.00","PUS":"9.00","PVG":"8.00","REP":"7.00","RGN":"6.50","ROI":"7.00","RTG":"8.00","RTI":"8.00","SBG":"7.00","SBW":"8.00","SDJ":"9.00","SDK":"8.00","SEA":"-8.00","SFO":"-8.00","SGN":"7.00","SIN":"8.00","SLL":"4.00","SMQ":"7.00","SNB":"7.00","SNK":"9.00","SNO":"7.00","SOC":"7.00","SOQ":"9.00","SQG":"7.00","SRG":"7.00","SRI":"8.00","SUB":"7.00","SVO":"3.00"';
        $jsonval .= ',"SWQ":"8.00","SXK":"9.00","SYD":"11.00","SZB":"8.00","SZX":"8.00","TAC":"8.00","TAG":"8.00","TAK":"9.00","TAO":"8.00","TGG":"8.00","TIM":"9.00","TJQ":"7.00","TJS":"8.00","TKG":"7.00","TLI":"8.00","TMC":"8.00","TNJ":"7.00","TPE":"8.00","TRK":"8.00","TRV":"5.50","TRZ":"5.50","TST":"7.00","TTE":"9.00","TWU":"8.00","TXL":"1.00","UBP":"7.00","UOL":"8.00","UPG":"8.00","URT":"7.00","USM":"7.00","UTH":"7.00","UTP":"7.00","VCE":"1.00","VTE":"7.00","VTZ":"5.50","WGP":"8.00","WMX":"9.00","WNI":"8.00","WUB":"9.00","WUH":"8.00","WUX":"8.00","XIY":"8.00","XMN":"8.00","YKR":"8.00","DEX":"9.00","MPC":"7.00","ZZZ":null}';
        $arraykota = json_decode($jsonval);
        foreach ($arraykota as $key => $value) {
            if ($key == $city) {
                $timeval = intval($value);
            }
        }

        if ($timeval == 1) {
            return "Europe/Amsterdam";
        } else if ($timeval == 2) {
            return "Africa/Blantyre";
        } else if ($timeval == 3) {
            return "Africa/Asmara";
        } else if ($timeval == 4) {
            return "Asia/Baku";
        } else if ($timeval == 5) {
            return "Asia/Aqtau";
        } else if ($timeval == 6) {
            return "Asia/Almaty";
        } else if ($timeval == 7) {
            return "Asia/Jakarta";
        } else if ($timeval == 8) {
            return "Asia/Makassar";
        } else if ($timeval == 9) {
            return "Asia/Jayapura";
        } else if ($timeval == 10) {
            return "Asia/Yakutsk";
        } else if ($timeval == 11) {
            return "Asia/Sakhalin";
        } else if ($timeval == 12) {
            return "Asia/Magadan";
        } else if ($timeval == 13) {
            return "Pacific/Apia";
        } else if ($timeval == 14) {
            return "Pacific/Kiritimati";
        }

        return "";
    }

    public function getStrukKERETA($data, $outlet)
    {

        $iskereta = ($data->data_pdk->group_produk_jp  === ProductCode::KERETA) ? 1 : 0;
        if ($iskereta) {
            $struk_name = "STRUK PEMBAYARAN TIKET KERETA";
            $bot_info = "CEK TIKET VIA KAI (www.kai.id)";
        } else {
            $struk_name = "STRUK PEMBAYARAN TIKET RAILINK";
            $bot_info = "CEK TIKET VIA RAILINK (URL)";
        }
        $booking_code = $data->bill_info2;
        $transportation_name = $data->bill_info78;
        $transportation_num = $data->bill_info15;
        $org = $data->bill_info79;
        $dest = $data->bill_info81;
        $time_depart = $this->getTime24($data->bill_info80);
        $time_arrive = $this->getTime24($data->bill_info82);
        $date_depart = strtoupper(date('d-M-Y', strtotime($data->bill_info13)));
        $i = 0;

        $strPass = "";

        $passenger  =  $data->bill_info20;
        $passenger .= ($data->bill_info24) ? "|" . $data->bill_info24 : "";
        $passenger .= ($data->bill_info28) ? "|" . $data->bill_info28 : "";
        $passenger .= ($data->bill_info32) ? "|" . $data->bill_info32 : "";

        $arrPassenger = explode("|", $passenger);


        foreach ($arrPassenger as $pass) {
            $i++;
            if (trim($pass) == "") {
                break;
            }

            $strPass .= $i . '. ' . $pass . ". *";
        }

        $nominaltiket = intval($data->nominal)  + intval($data->nominal_admin)  + intval($data->bill_info14);

        return $this->alignLeft('CU') . '*' .
            $this->alignLeft($struk_name) . '*' .
            $this->alignLeft('KODE BOOKING') . ': ' . $booking_code . '*' . '*' .
            $this->alignLeft('NO. RESI') . ': ' . $data->id_transaksi . '*' .
            $this->alignLeft('NO KA') . ': ' . $transportation_num . '*' .
            $this->alignLeft('NAMA KA') . ': ' . $transportation_name . '*' .
            $this->alignLeft('TGL BERANGKAT') . ': ' . $date_depart . '*' .
            $this->alignLeft('STASIUN/JAM ASL') . ': ' . $org . ',' . $time_depart . '*' .
            $this->alignLeft('STASIUN/JAM TJN') . ': ' . $dest . ',' . $time_arrive . '*' .
            $this->alignLeft('JML PENUMPANG') . ': ' . count($arrPassenger) . '*' .
            $this->alignLeft('NO KURSI') . ': ' . $data->bill_info83 .
            '**INFO PENUMPANG*' .
            $this->alignLeft('PENUMPANG') . ':*' . $strPass . '*' . '*' .
            //$this->alignLeft('NOMINAL TIKET').': Rp ' . number_format($data->nominal, "0", ",", ".") . '*'.
            //$this->alignLeft('BOOKING FEE').': Rp 0*------------------*'.
            $this->alignLeft('TOTAL BAYAR') . ': Rp ' .
            number_format($nominaltiket, "0", ",", ".") . '*' .
            $this->alignLeft('TERBILANG') . ':*' .
            $this->ww($this->terbilang($data->nominal), 1) . ' RUPIAH**' . $struk_name . ' YANG SAH*...*' . $bot_info . '*' .
            $this->alignLeft('ID OUTLET') . ': ' . $outlet->idOutlet . '*';
    }
    public function getStrukKAPAL($data, $outlet)
    {

        $struk_name = "STRUK PEMBAYARAN TIKET PELNI";
        $bot_info = "CEK TIKET VIA PELNI (www.pelni.co.id)";
        $resi_num = $data->id_transaksi;
        $booking_code = (!$data->bill_info1) ? "*(kode booking didapat setelah payment)" : $data->bill_info1;
        $transportation_name = $data->bill_info78;
        $transportation_num = $data->bill_info15;
        $json_info = json_decode($data->bill_info70);
        $org = $json_info->data_pelabuhan_asal;
        $dest = $json_info->data_pelabuhan_tujuan;
        $time_depart = $this->getTime24($data->bill_info30);
        $time_arrive = $this->getTime24($data->bill_info31);
        $date_depart = strtoupper(date('d-M-Y', strtotime($data->bill_info24)));
        $date_arrive = strtoupper(date('d-M-Y', strtotime($data->bill_info29)));
        $strPass = "";

        $Passenger = explode("|", $data->bill_info75);
        $Passenger_identity_no = explode("|", $data->bill_info61);

        for ($i = 0; $i < sizeof($Passenger); $i++) {

            $strPass .= $i + 1 . '. ' . $Passenger[$i] . " / " . $Passenger_identity_no[$i] . ". *";
        }

        $nominalbayar = intval($data->nominal) + intval($data->nominal_admin);
        $countPax = intval($data->bill_info26) + intval($data->bill_info27) + intval($data->bill_info28);

        return $this->alignLeft('CU') . '*' .
            $this->alignLeft($struk_name) . '*' .
            $this->alignLeft('KODE BOOKING') . ': ' . $booking_code . '*' . '*' .
            $this->alignLeft('NO. RESI') . ': ' . $resi_num . '*' .
            //$this->alignLeft('NO KA').': ' . $transportation_num . '*'.
            //$this->alignLeft('NAMA KA').': ' . $transportation_name . '*'.
            $this->alignLeft('PEL ASL') . ': ' . $org . '*' .
            $this->alignLeft('PEL TJN') . ': ' . $dest . '*' .
            $this->alignLeft('TGL/JAM ASL') . ': ' . $date_depart . ',' . $time_depart . '*' .
            $this->alignLeft('TGL/JAM TJN') . ': ' . $date_arrive . ',' . $time_arrive . '*' .
            $this->alignLeft('JML PENUMPANG') . ': ' . $countPax .
            '**INFO PENUMPANG*' .
            $this->alignLeft('PENUMPANG') . ':*' . $strPass . '*' . '*' .
            //$this->alignLeft('NOMINAL TIKET').': Rp ' . number_format($data->nominal, "0", ",", ".") . '*'.
            //$this->alignLeft('BOOKING FEE').': Rp 0*------------------*'.
            $this->alignLeft('TOTAL BAYAR') . ': Rp ' .
            number_format($nominalbayar, "0", ",", ".") . '*' .
            $this->alignLeft('TERBILANG') . ':*' .
            $this->ww($this->terbilang($nominalbayar), 1) . ' RUPIAH**' . $struk_name . ' YANG SAH*...*' . $bot_info . '*' .
            $this->alignLeft('ID OUTLET') . ': ' . $outlet->idOutlet . '*';
    }
    public function getStrukTRAVEL($data, $outlet)
    {

        $struk_name = "STRUK PEMBAYARAN TIKET TRAVEL";
        $bot_info = "CEK TIKET VIA TRAVEL (URL)";

        $booking_code = $data->bill_info1;
        $transportation_code = $data->bill_info10;

        $query_agen = $this->db->query("SELECT nama FROM tiketux_mt_agen WHERE kode_agen = ?", [$transportation_code]);
        $query_agen->setFetchMode(Db::FETCH_OBJ);

        $agen = $query_agen->fetch();
        $transportation_name = $agen->nama;
        //$transportation_num = $data->bill_info15;
        $contact_person = $data->bill_info29;
        $org = $data->bill_info12 . ' (' . $data->bill_info11 . ')';
        $dest = $data->bill_info14 . ' (' . $data->bill_info13 . ')';
        $time_depart = $this->getTime24($data->bill_info16);
        //$time_arrive = $this->getTime24($data->bill_info82);
        $date_depart = strtoupper(date('d-M-Y', strtotime($data->bill_info15)));
        $i = 0;

        $strPass = "";

        $passenger  =  $data->bill_info31;
        $passenger .= ($data->bill_info32) ? "|" . $data->bill_info32 : "";
        $passenger .= ($data->bill_info33) ? "|" . $data->bill_info33 : "";

        $arrPassenger = explode("|", $passenger);


        foreach ($arrPassenger as $pass) {
            $i++;
            if (trim($pass) == "") {
                break;
            }
            $strPass .= $i . '. ' . $pass . ". *";
        }

        return $this->alignLeft('CU') . '*' .
            $this->alignLeft($struk_name) . '*' . '*' .
            $this->alignLeft('NO. RESI') . ': ' . $data->id_transaksi . '*' .
            $this->alignLeft('KODE BOOKING') . ': ' . $booking_code . '*' . '*' .
            //$this->alignLeft('NO TRAVEL').': ' . $transportation_num . '*'.
            $this->alignLeft('NAMA TRAVEL') . ': ' . $transportation_name . '*' .
            $this->alignLeft('TGL BERANGKAT') . ': ' . $date_depart . '*' .
            $this->alignLeft('JAM BERANGKAT') . ': ' . $time_depart . '*' .
            $this->alignLeft('ASAL') . ': ' . $org . '*' .
            $this->alignLeft('TUJUAN') . ': ' . $dest . '*' .
            $this->alignLeft('JML PENUMPANG') . ': ' . count($arrPassenger) .
            '**INFO PENUMPANG*' .
            $this->alignLeft('KONTAK') . ': ' . '+' . $contact_person . '*' .
            $this->alignLeft('PENUMPANG') . ':*' . $strPass . '*' . '*' .
            //$this->alignLeft('NOMINAL TIKET').': Rp ' . number_format($data->nominal, "0", ",", ".") . '*'.
            //$this->alignLeft('BOOKING FEE').': Rp 0*------------------*'.
            $this->alignLeft('TOTAL BAYAR') . ': Rp ' .
            number_format($data->nominal, "0", ",", ".") . '*' .
            $this->alignLeft('TERBILANG') . ':*' .
            $this->ww($this->terbilang($data->nominal), 1) . ' RUPIAH**' . $struk_name . ' YANG SAH*...*' . $bot_info . '*' .
            $this->alignLeft('ID OUTLET') . ': ' . $outlet->idOutlet . '*';
    }

    public function getStrukWISATA($data, $outlet)
    {

        $struk_name = "STRUK PEMBAYARAN PAKET WISATA";
        $bot_info = "CEK TIKET VIA WISATA (URL)";

        $booking_code = $data->bill_info1;
        $transportation_code = $data->bill_info10;

        $query_agen = $this->db->query("SELECT nama FROM tiketux_mt_agen WHERE kode_agen = ?", [$transportation_code]);
        $query_agen->setFetchMode(Db::FETCH_OBJ);

        $agen = $query_agen->fetch();
        $transportation_name = $agen->nama;
        //$transportation_num = $data->bill_info15;
        $contact_person = $data->bill_info31;
        $org = $data->bill_info12 . ' (' . $data->bill_info11 . ')';
        $dest = $data->bill_info14 . ' (' . $data->bill_ * info13 . ')';
        $time_depart = $this->getTime24($data->bill_info16);
        //$time_arrive = $this->getTime24($data->bill_info82);
        $date_depart = strtoupper(date('d-M-Y', strtotime($data->bill_info24)));
        $date_arrive = strtoupper(date('d-M-Y', strtotime($data->bill_info25)));
        $i = 0;

        $strPass = "";

        $passenger  =  $data->bill_info30;
        //$passenger .= ($data->bill_info32)?"|" . $data->bill_info32:"";        
        //$passenger .= ($data->bill_info33)?"|" . $data->bill_info33:"";

        $arrPassenger = explode("|", $passenger);


        foreach ($arrPassenger as $pass) {
            $i++;
            if (trim($pass) == "") {
                break;
            }
            $strPass .= $i . '. ' . $pass . ". *";
        }

        return $this->alignLeft('CU') . '*' .
            $this->alignLeft($struk_name) . '*' . '*' .
            $this->alignLeft('NO. RESI') . ': ' . $data->id_transaksi . '*' .
            $this->alignLeft('KODE BOOKING') . ': ' . $booking_code . '*' . '*' .
            //$this->alignLeft('NO TRAVEL').': ' . $transportation_num . '*'.
            $this->alignLeft('NAMA WISATA') . ': ' . $transportation_name . '*' .
            $this->alignLeft('TGL BERANGKAT') . ': ' . $date_depart . '*' .
            $this->alignLeft('TGL PULANG') . ': ' . $date_arrive . '*' .

            //$this->alignLeft('JML PESERTA').': ' .count($arrPassenger) . 
            $this->alignLeft('JML PESERTA') . ': ' . $data->bill_info32 .
            '**INFO PESERTA *' .
            $this->alignLeft('KONTAK') . ': ' . '+' . $contact_person . '*' .
            $this->alignLeft('PESERTA') . ':*' . $strPass . '*' . '*' .
            //$this->alignLeft('NOMINAL TIKET').': Rp ' . number_format($data->nominal, "0", ",", ".") . '*'.
            //$this->alignLeft('BOOKING FEE').': Rp 0*------------------*'.
            $this->alignLeft('TOTAL BAYAR') . ': Rp ' .
            number_format($data->nominal, "0", ",", ".") . '*' .
            $this->alignLeft('TERBILANG') . ':*' .
            $this->ww($this->terbilang($data->nominal), 1) . ' RUPIAH**' . $struk_name . ' YANG SAH*...*' . $bot_info . '*' .
            $this->alignLeft('ID OUTLET') . ': ' . $outlet->idOutlet . '*';
    }
    public function getStrukHOTEL($data, $outlet)
    {

        $struk_name = "STRUK PEMBAYARAN HOTEL";
        $bot_info = "CEK BOOKING VIA HOTEL (URL)";

        $qHotel = $this->db->query("SELECT nama_hotel FROM hotel_data_detail_3 WHERE id_hotel_biller = ?", [$data->bill_info25]);
        $qHotel->setFetchMode(Db::FETCH_OBJ);
        $dataHotel = $qHotel->fetch();

        $booking_code = $data->bill_info2;
        $json_info = json_decode($data->bill_info75);
        $transportation_code = $data->bill_info10;
        $produk_name = $data->HotelName;
        //$transportation_num = $data->bill_info15;
        $pemesan = "";
        $time_depart = $data->bill_info35;
        $contact_person = $json_info[0]->RoomCatg[0]->RoomType[0]->PaxInformation[0]->no_hp_pax;

        $org = $data->bill_info12 . ' (' . $data->bill_info11 . ')';
        $dest = $data->bill_info14 . ' (' . $data->bill_ * info13 . ')';
        $time_depart = $this->getTime24($data->bill_info16);
        $special_req = $data->bill_info46;
        //$time_arrive = $this->getTime24($data->bill_info82);
        $date_checkin = strtoupper(date('d-M-Y', strtotime($json_info[0]->RoomCatg[0]->checkIn)));
        $date_checkout = strtoupper(date('d-M-Y', strtotime($json_info[0]->RoomCatg[0]->checkOut)));
        $i = 0;

        $strPass = "";

        $passenger  =  $data->bill_info4;
        //$passenger .= ($data->bill_info32)?"|" . $data->bill_info32:"";        
        //$passenger .= ($data->bill_info33)?"|" . $data->bill_info33:"";

        $arrPassenger = explode(";", $passenger);


        foreach ($arrPassenger as $pass) {
            $i++;
            if (trim($pass) == "") {
                break;
            }
            $strPass .= $i . '. ' . $pass . ". *";
        }

        return $this->alignLeft('CU') . '*' .
            $this->alignLeft($struk_name) . '*' . '*' .
            $this->alignLeft('NO. RESI') . ': ' . $data->id_transaksi . '*' .
            $this->alignLeft('KODE BOOKING') . ': ' . $booking_code . '*' . '*' .
            //$this->alignLeft('NO TRAVEL').': ' . $transportation_num . '*'.
            $this->alignLeft('NAMA HOTEL') . ': ' . $produk_name . '*' .
            $this->alignLeft('TGL CHECK IN') . ': ' . $date_checkin . '*' .
            $this->alignLeft('TGL CHECK OUT') . ': ' . $date_checkout . '*' .

            $this->alignLeft('JML KAMAR') . ': ' . $data->bill_info35 .
            '**INFO PESERTA *' .
            $this->alignLeft('KONTAK') . ': ' . '+' . $contact_person . '*' .
            $this->alignLeft('TAMU') . ':*' . $data->bill_info41 . '*' . '*' .
            //$this->alignLeft('NOMINAL BAYAR').': Rp ' . number_format($data->nominal, "0", ",", ".") . '*'.
            //$this->alignLeft('BOOKING FEE').': Rp 0*------------------*'.
            $this->alignLeft('TOTAL BAYAR') . ': Rp ' .
            number_format($data->nominal, "0", ",", ".") . '*' .
            $this->alignLeft('TERBILANG') . ':*' .
            $this->ww($this->terbilang($data->nominal), 1) . ' RUPIAH**' . $struk_name . ' YANG SAH*...*' . $bot_info . '*' .
            $this->alignLeft('ID OUTLET') . ': ' . $outlet->idOutlet . '*';
    }
    // Get 24H format if any existing function please fix me
    public function getTime24($time)
    {
        if (!strpos($time, ':'))
            $time = substr_replace($time, ':', 2, 0);

        return $time;
    }
    public function get_city_name($city_code)
    {
        $city = $this->db->query("select city_name from fmss.mt_destination where city_code=?", [$city_code]);
        $city->setFetchMode(Db::FETCH_OBJ);
        $data = $city->fetch();
        return $data->city_name;
    }

    public function alignLeft($title)
    {
        $arr = $title;
        $t = 15 - strlen($title);
        for ($i = 0; $i < $t; $i++) {
            $arr .= '|';
        }
        return $arr;
    }

    public function getNamaMaskapai($id_produk)
    {
        $city = $this->db->query("select id_produk,produk from mt_produk where id_produk in(select id_produk from ft_mt_produk where group_produk_jp = 'PESAWAT') and id_produk = ? ", [$id_produk]);
        $city->setFetchMode(Db::FETCH_OBJ);
        $data = $city->fetch();
        return $data->produk;
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
}