<?php

namespace Travel\Train;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\HttpConnect;
use Travel\Libraries\Parser\Train\PaymentOnlineBookResponseParser;
use Travel\Libraries\MTI;
use Travel\Libraries\Utility;

class PaymentOnlineBookController extends APIController
{
    protected $invoking = "Payment Online Booking Train";

    public function indexAction()
    {
        $paymentCode = $this->request->paymentCode;

        $outletData = $this->getOutletData($this->getOutletId());

        if (substr($paymentCode, 0, 3) == "789") {
            $message = array($paymentCode, $this->getOutletId(), $this->getPin(), $this->config->trainOnlineBook->via);

            $urlTarget = "http://" . $this->config->trainOnlineBook->host . ":"
                . $this->config->trainOnlineBook->port . "/"
                . $this->config->trainOnlineBook->path . "/"
                . $this->config->trainOnlineBook->payment . "?payment_desktop=" . urlencode(implode(':', $message));

            $this->request->simulateSuccess = $this->config->environment;

            if (!$this->request->simulateSuccess) {
                if (!Utility::isTesterOutlet($this->getOutletId())) {
                    $response = HttpConnect::sendToURL($urlTarget, $config->trainOnlineBook->port, "GET");
                }
            } else {
                $response = '{"err_code":"0","result":"6NYBMD:MANDIRI:081288701480:PWS:JNG:100000:6603#0#1700:umum:2:FA30880:TRI KURNIAWAN SETIADI:8009120512217:BENGAWAN:PURWOSARI:1535:JATINEGARA:0041:Ekonomi:K3AC-4/7A,K3AC-4/7B:2:0#0:50000:50000#5000:100000:0#0:106603:TRI KURNIAWAN SETIADI:trikurniawan.setiadi@gmail.com:20140405:05-April-2014:125L:672226:177579052:WEB:TRI KURNIAWAN SETIADI,YULIARTI,,,,,,,,,,:BANK BCA:1220004267475:TRI KURNIAWAN SETIADI"}';
            }

            //$response = '{"err_code":"0","result":"6NYBMD:MANDIRI:081288701480:PWS:JNG:100000:6603#0#1700:umum:2:FA30880:TRI KURNIAWAN SETIADI:8009120512217:BENGAWAN:PURWOSARI:1535:JATINEGARA:0041:Ekonomi:K3AC-4/7A,K3AC-4/7B:2:0#0:50000:50000#5000:100000:0#0:106603:TRI KURNIAWAN SETIADI:trikurniawan.setiadi@gmail.com:20140405:05-April-2014:125L:672226:177579052:WEB:TRI KURNIAWAN SETIADI,YULIARTI,,,,,,,,,,:BANK BCA:1220004267475:TRI KURNIAWAN SETIADI"}';

            $rc = "xx";
            $rd = "Pembayaran gagal. Silahkan coba beberapa saat lagi";
            $responseData = json_decode($response);
            if ($responseData === TRUE) {
                $rc = $responseData->err_code;
                $rd = $responseData->result;
            }

            if ($rc == "0") {
                $is_railink = false;

                $dataResult = $rd;

                if (sizeof(explode("~", $dataResult)) > 1) {

                    if (strpos(explode("~", $dataResult)[1], "RAIL") === 0) {
                        $dataResult = str_replace("~RAIL", "", $dataResult);

                        $is_railink = true;
                    }
                }

                $data = explode(':', $dataResult);

                $nama_outlet = is_null($outletData->nama_outlet) || trim($outletData->nama_outlet) == "" || trim($outletData->nama_outlet) == "NAMA OUTLET ANDA" ? $outletData->nama_pemilik : $outletData->nama_outlet;

                $alamat_outlet = is_null($outletData->alamat_outlet) || trim($outletData->alamat_outlet) == "" || trim($outletData->alamat_outlet) == "ALAMAT OUTLET ANDA" ? $outletData->alamat_pemilik : $outletData->alamat_outlet;

                $no_telp_outlet = is_null($outletData->notelp_outlet) || trim($outletData->notelp_outlet) == "" || trim($outletData->notelp_outlet) == "NO. TELP ANDA" ? $outletData->notelp_pemilik : $outletData->notelp_outlet;

                $nama_kota = $outletData->nama_kota;

                $arrAdmin = explode('#', $data[6]);

                $nominal_admin = $arrAdmin[0];

                $voucher = $arrAdmin[1];

                //$diskon =  -1 * (7500 - intval($nominal_admin));
                $diskon =  -1 * (intval($nominal_admin));

                if ($is_railink) {
                    $diskon =  -1 * (0 - intval($nominal_admin));
                }

                $data_jumlah = explode('#', $data[20]);

                $data_harga = explode('#', $data[22]);

                $diskon_kai = $arrAdmin[2];

                $total_bayar_payment_code = intval(trim($data[5])) + intval($nominal_admin) + intval($diskon_kai);

                $message_struk = array(
                    "kode_produk" => "MKAI",
                    "book_code" => $data[0],
                    "train_no" => $data[30],
                    "name" => $data[10],
                    "train_name" => $data[12],
                    "dep_date" => date('d M Y', strtotime($data[28])),
                    "origination" => $data[13],
                    "departure_time" => $this->formatWaktu($data[14]),
                    "payment_date" => date('d M Y'),
                    "destination" => $data[15],
                    "arrival_time" => $this->formatWaktu($data[16]),
                    "payment_time" => date('H:i'),
                    "class" => $data[17],
                    "seat_number" => $data[18],
                    "adult_pax_number" => $data[19],
                    "adult_price" => $data[21],
                    "child_pax_number" => $data_jumlah[0],
                    "child_price" => $data_harga[0],
                    "infant_pax_number" => $data_jumlah[1],
                    "infant_price" => $data_harga[1],
                    "admin_fee" => $nominal_admin,
                    "id_outlet" => $this->getOutletId() . " (" . $nama_outlet . ")",
                    "alamat" => $alamat_outlet . ", " . $nama_kota,
                    "telepon" => $no_telp_outlet,
                    "discount" => $diskon_kai
                );

                if ($is_railink) {
                    $message_struk = array(
                        "kode_produk" => "RAILM",
                        "book_code" => $data[0],
                        "train_no" => $data[30],
                        "name" => $data[10],
                        "train_name" => $data[12],
                        "dep_date" => date('d M Y', strtotime($data[28])),
                        "origination" => $data[13],
                        "departure_time" => $this->formatWaktu($data[14]),
                        "payment_date" => date('d M Y'),
                        "destination" => $data[15],
                        "arrival_time" => $this->formatWaktu($data[16]),
                        "payment_time" => date('H:i'),
                        "class" => $data[17],
                        "seat_number" => $data[18],
                        "adult_pax_number" => $data[19],
                        "adult_price" => $data[21],
                        "child_pax_number" => $data_jumlah[0],
                        "child_price" => $data_harga[0],
                        "infant_pax_number" => $data_jumlah[1],
                        "infant_price" => $data_harga[1],
                        "admin_fee" => $nominal_admin,
                        "id_outlet" => $this->getOutletId() . " (" . $nama_outlet . ")",
                        "alamat" => $alamat_outlet . ", " . $nama_kota,
                        "telepon" => $no_telp_outlet,
                        "discount" => $diskon_kai
                    );
                }

                $urlStruk = "http://" . $this->config->pdfGenerator->host . ":"
                    . $this->config->pdfGenerator->port . "/"
                    . $this->config->pdfGenerator->path . "/kaiStrukReceiver.php";

                $struk = HttpConnect::sendToURL($urlStruk, $this->config->pdfGenerator->port, json_encode($message_struk));

                $t = json_decode($struk->response);

                //'url_struk_pdf' => $this->config->pdfGenerator->pathpdf . $message->get(TrainMessage::FIELD_TRX_ID).'.pdf'

                $arrPayment = array(
                    'err_code' => intval($rc),
                    'book_code' => $data[0],
                    'saldo' => $outletData->balance,
                    'url_struk_image' => $this->config->pdfGenerator->pathimage . $t->url_struk

                );

                $this->response->data[] = $arrPayment;
            }

            $this->response->setStatus($rc == "0" ? "00" : $rc, $rc == "00" ? "Success" : $rd);
        } else {

            $book_code = "";

            $numcode = "";

            if (preg_match("/[A-Z]/", $paymentCode) && strlen($paymentCode) == 6) {
                $book_code = $paymentCode; // bookcode 
            } else {
                $numcode = $paymentCode; // numcode
            }

            if ($this->config->trainOnlineBook->via === "DESKTOP") {
                if ($this->getKey() === "XYZ1") //Allow key travel
                {
                    $arr = array('err_code' => "1", 'err_msg' => "Key anda tidak cocok / telah kadaluarsa .. ");

                    $response = json_encode($arr);

                    die(trim($response));
                }
            }

            $this->setMTI(MTI::NKAIOBPAY);
            $this->setProductCode($this->request->productCode);

            $message = new TrainMessage($this);

            $message->set(TrainMessage::FIELD_ID_PEL2, $book_code);
            $message->set(TrainMessage::FIELD_ID_PEL3, $numcode);
            $message->set(TrainMessage::FIELD_PAY_TYPE, "TUNAI");
            $message->set(TrainMessage::FIELD_TRX_ID, $this->request->transactionId);
            $message->set(TrainMessage::FIELD_VIA, "DESKTOP");
            $message->set(TrainMessage::FIELD_TOKEN, "travel");

            /* SIMULATOR */
            //$result = 'NKAIBOKINF*PKAI*324949902*6*20140313135252*DESKTOP***9992900631707*50000**BS0003*131313*travel*333850*1***170681588*00*Success***SGU*LPN*22-MAR-14*22-MAR-14**138*EKONOMI*C*1*0*0*SUROTO***3402102205840001*****************************087839143666*9992900631707*N2712P**50000**57500******11*E****************TUNAI**UMUM*1**SRI TANJUNG*SURABAYA*1345*LEMPUYANGAN*1937*K3AC-4/11E***';
            //$result = 'NKAIGPAY*PKAI*324950234*10*20140313135310*DESKTOP***9992900631707*50000*7500*BS0003*131313*travel*276350*1***170681691*00*Success***SGU*LPN*22-MAR-14*22-MAR-14**138*EKONOMI*C*1*0*0*SUROTO***3402102205840001*****************************087839143666*9992900631707*N2712P*K3AC-4/11E*50000**0******11*E****************TUNAI**UMUM*1**SRI TANJUNG*SURABAYA*1345*LEMPUYANGAN*1937*K3AC-4/11E*50000*0*0';
            //$message->parse('NKAIGPAY*PKAI*324950234*10*20140313135310*DESKTOP***9992900631707*50000*7500*BS0003*131313*travel*276350*1***170681691*00*Success***SGU*LPN*22-MAR-14*22-MAR-14**138*EKONOMI*C*1*0*0*SUROTO***3402102205840001*****************************087839143666*9992900631707*N2712P*K3AC-4/11E*50000**0******11*E****************TUNAI**UMUM*1**SRI TANJUNG*SURABAYA*1345*LEMPUYANGAN*1937*K3AC-4/11E*50000*0*0');

            $this->request->simulateSuccess = $this->config->environment;

            if (!$this->request->simulateSuccess) {
                if (!Utility::isTesterOutlet($this->getOutletId())) {
                    $this->sendToCore($message);
                    //$message->parse('NKAIGPAY*PKAI*2452241548*8*20180306205327*DESKTOP**AQVZGQ*9998013987208*35000*7500*FA0011*169515*travel*3573856*2***944061711*00*Success***ML*SGU*30-APR-18*30-APR-18**143*EKONOMI*C*1*0*0*IMAM SYAFII**085730509050*3578062007840004*****************************085730509050*9998013987208*AQVZGQ*EKO-1/1B*35000**0*-7500*****1*B****************TUNAI**UMUM*1**JAYABAYA*MALANG*1145*SURABAYA GUBENG*1341*EKO-1/1B*35000*0*0');
                }
            } else {
                $message->parse('NKAIGPAY*PKAI*2452241548*8*20180306205327*DESKTOP**AQVZGQ*9998013987208*35000*7500*FA0011*169515*travel*3573856*2***944061711*00*Success***ML*SGU*30-APR-18*30-APR-18**143*EKONOMI*C*1*0*0*IMAM SYAFII**085730509050*3578062007840004*****************************085730509050*9998013987208*AQVZGQ*EKO-1/1B*35000**0*-7500*****1*B****************TUNAI**UMUM*1**JAYABAYA*MALANG*1145*SURABAYA GUBENG*1341*EKO-1/1B*35000*0*0');
            }

            PaymentOnlineBookResponseParser::instance()->parse($message)->into($this);

            if ($message->get(TrainMessage::FIELD_STATUS) == "00") {
                $nama_outlet = is_null($outletData->nama_outlet) || trim($outletData->nama_outlet) == "" || trim($outletData->nama_outlet) == "NAMA OUTLET ANDA" ? $outletData->nama_pemilik : $outletData->nama_outlet;

                $alamat_outlet = is_null($outletData->alamat_outlet) || trim($outletData->alamat_outlet) == "" || trim($outletData->alamat_outlet) == "ALAMAT OUTLET ANDA" ? $outletData->alamat_pemilik : $outletData->alamat_outlet;

                $no_telp_outlet = is_null($outletData->notelp_outlet) || trim($outletData->notelp_outlet) == "" || trim($outletData->notelp_outlet) == "NO. TELP ANDA" ? $outletData->notelp_pemilik : $outletData->notelp_outlet;

                $nama_kota = $outletData->nama_kota;

                $message_struk = array(
                    "kode_produk" => $message->get(TrainMessage::FIELD_KODE_PRODUK),
                    "book_code" => $message->get(TrainMessage::FIELD_BOOK_CODE),
                    "train_no" => $message->get(TrainMessage::FIELD_TRAIN_NO),
                    "name" => $message->get(TrainMessage::FIELD_ADULT_NAME1),
                    "train_name" => $message->get(TrainMessage::FIELD_TRAIN_NAME),
                    "dep_date" => $message->get(TrainMessage::FIELD_DEP_DATE),
                    "origination" => $message->get(TrainMessage::FIELD_ORIGINATION),
                    "departure_time" => $this->formatWaktu($message->get(TrainMessage::FIELD_ARV_TIME)),
                    "payment_date" => date('d M Y'),
                    "destination" => $message->get(TrainMessage::FIELD_DESTINATION),
                    "arrival_date" => $message->get(TrainMessage::FIELD_ARV_DATE),
                    "arrival_time" => $this->formatWaktu($message->get(TrainMessage::FIELD_ARV_TIME)),
                    "payment_time" => date('H:i'),
                    "class" => $message->get(TrainMessage::FIELD_CLASS),
                    "seat_number" => $message->get(TrainMessage::FIELD_SEAT),
                    "adult_pax_number" => $message->get(TrainMessage::FIELD_NUM_PAX_ADULT),
                    "adult_price" => intval(intval($message->get(TrainMessage::FIELD_PRICE_ADULT)) / intval($message->get(TrainMessage::FIELD_NUM_PAX_ADULT))),
                    "child_pax_number" => $message->get(TrainMessage::FIELD_NUM_PAX_CHILD),
                    "child_price" => intval(intval($message->get(TrainMessage::FIELD_PRICE_CHILD)) / (intval($message->get(TrainMessage::FIELD_NUM_PAX_CHILD)) == 0) ? 1 : intval($message->get(TrainMessage::FIELD_NUM_PAX_CHILD))),
                    "infant_pax_number" => $message->get(TrainMessage::FIELD_NUM_PAX_INFANT),
                    "infant_price" => intval(intval($message->get(TrainMessage::FIELD_PRICE_INFANT)) /  (intval($message->get(TrainMessage::FIELD_NUM_PAX_INFANT)) == 0) ? 1 : intval($message->get(TrainMessage::FIELD_NUM_PAX_INFANT))),
                    "admin_fee" => $message->get(TrainMessage::FIELD_NOMINAL_ADMIN),
                    "id_outlet" => $message->get(TrainMessage::FIELD_LOKET_ID) . " (" . $nama_outlet . ")",
                    "alamat" => $alamat_outlet . ", " . $nama_kota,
                    "telepon" => $no_telp_outlet,
                    "discount" => intval($message->get(TrainMessage::FIELD_SEAT_MAP_NULL))
                );

                $urlStruk = "http://" . $this->config->pdfGenerator->host . ":"
                    . $this->config->pdfGenerator->port . "/"
                    . $this->config->pdfGenerator->path . "/kaiStrukReceiver.php";

                $struk = HttpConnect::sendToURL($urlStruk, $this->config->pdfGenerator->port, json_encode($message_struk));

                $t = json_decode($struk->response);

                $arrPayment = array(
                    'err_code' => intval($message->get(TrainMessage::FIELD_STATUS)),
                    'book_code' => $message->get(TrainMessage::FIELD_BOOK_CODE),
                    'saldo' => $outletData->balance,
                    'url_struk_image' => $this->config->pdfGenerator->pathimage . $t->url_struk,
                    'url_struk_pdf' => $this->config->pdfGenerator->pathpdf . $message->get(TrainMessage::FIELD_TRX_ID) . '.pdf'
                );

                if ($message->get(TrainMessage::FIELD_STATUS) == "00") {
                    $this->response->rc = "00";
                    $this->response->rd = "Sukses";
                }
            } else {
                $balance = 0;

                if ($this->config->trainOnlineBook->via === "YM") {
                    $balance = $outletData->balance; /*Balance by YM*/
                } else {
                    $balance = $outletData->balance;
                }

                $arrPayment = array('err_code' => $message->get(TrainMessage::FIELD_STATUS), 'err_msg' => $message->get(TrainMessage::FIELD_KETERANGAN)) + array("saldo" => $balance);

                if (intval($message->get(TrainMessage::FIELD_STATUS)) == 12) {
                    $this->response->rc = '12';
                    $this->response->rd = 'SALDO ANDA TIDAK CUKUP';
                }
            }

            $this->response->data[] = $arrPayment;
        }
    }

    public function formatWaktu($strtime)
    {
        return $strtime;
    }
}