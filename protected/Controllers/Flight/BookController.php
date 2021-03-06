<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\BookResponseParser;
use Travel\Libraries\Utility;
use Travel\Libraries\MTI;
use DateTime;

class BookController extends APIController
{
    protected $invoking = "Book Flight";

    public function indexAction()
    {
        $this->setMTI(MTI::RESERVATION);
        $this->setProductCode($this->request->airline);

        $message = new FlightMessage($this);

        $message->set(FlightMessage::FIELD_FLIGHT_STEP, "BOOKING");
        $message->set(FlightMessage::FIELD_INDEX, date("YmdHis"));
        $message->set(FlightMessage::FIELD_MASKAPAI, $this->request->airline);
        $message->set(FlightMessage::FIELD_RUTE, 0);

        $departureDate = DateTime::createFromFormat('Y-m-d', $this->request->departureDate);

        $message->set(FlightMessage::FIELD_CITY_ORIGIN, $this->request->departure);
        $message->set(FlightMessage::FIELD_CITY_DESTINATION, $this->request->arrival);
        $message->set(FlightMessage::FIELD_DATE_DEPARTURE, $departureDate->format('m/d/Y'));
        $message->set(FlightMessage::FIELD_DATE_ARRIVAL, $departureDate->format('m/d/Y'));
        $message->set(FlightMessage::FIELD_COUNT_ADULT, $this->request->adult);
        $message->set(FlightMessage::FIELD_COUNT_CHILD, $this->request->child);
        $message->set(FlightMessage::FIELD_COUNT_BABY, $this->request->infant);

        $message->set(FlightMessage::FIELD_PROCESS, "01");

        // bagasi khususon lion
        if ($this->request->airline === "TPJT" && $this->request->baggages != null) {
            $message->set(FlightMessage::FIELD_LOKET_ID, "BS0004");
            $message->set(FlightMessage::FIELD_PIN, "141414");
            $message->set(FlightMessage::FIELD_TOKEN, "travel");

            $baggages = $this->request->baggages;
            $arrBaggage = array();
            for ($i = 0; $i < count($baggages); $i++) {
                $arrDetail = array();
                $details = (array) $baggages[$i];
                for ($j = 0; $j < count($details); $j++) {
                    $arrDetail[] = $details[$j]->kode_maskapai . ";" . $details[$j]->baggage_key . ";" . $details[$j]->weight;
                }
                $arrBaggage[] = implode("TRANSIT", $arrDetail);
            }

            $message->set(FlightMessage::FIELD_STATUS, implode("SEPARATOR", $arrBaggage));
        }

        for ($i = 0; $i < count($this->request->flights); $i++) {
            $message->set(FlightMessage::FIELD_CLASSNAME1 + $i, $this->request->flights[$i]);
        }

        for ($i = 0; $i < count($this->request->passengers->adults); $i++) {
            //$message->set(FlightMessage::FIELD_ADULT1 + ($i * 3), str_replace(';MISS;', ';MS;', $this->request->passengers->adults[$i]) );
            $message->set(FlightMessage::FIELD_ADULT1 + ($i * 3), str_ireplace(";null;", ";;", str_replace(';MISS;', ';MS;', $this->request->passengers->adults[$i])));
        }

        if (isset($this->request->passengers->children)) {
            for ($i = 0; $i < count($this->request->passengers->children); $i++) {
                //$message->set(FlightMessage::FIELD_CHILD1 + ($i * 3), $this->request->passengers->children[$i]);
                if ($this->request->airline === "TPSJ") {
                    $message->set(FlightMessage::FIELD_CHILD1 + ($i * 3), self::setToMstrMiss(str_ireplace(";null;", ";;", $this->request->passengers->children[$i])));
                } else {
                    $message->set(FlightMessage::FIELD_CHILD1 + ($i * 3), str_ireplace(";null;", ";;", $this->request->passengers->children[$i]));
                }
            }
        }

        if (isset($this->request->passengers->infants)) {
            for ($i = 0; $i < count($this->request->passengers->infants); $i++) {
                //$message->set(FlightMessage::FIELD_INFAN1 + ($i * 3), $this->request->passengers->infants[$i]);
                if ($this->request->airline === "TPSJ") {
                    $message->set(FlightMessage::FIELD_INFAN1 + ($i * 3), self::setToMstrMiss(str_ireplace(";null;", ";;", $this->request->passengers->infants[$i])));
                } else {
                    $message->set(FlightMessage::FIELD_INFAN1 + ($i * 3), str_ireplace(";null;", ";;", $this->request->passengers->infants[$i]));
                }
            }
        }

        $flights = intval($this->request->flights);
        $message->set(FlightMessage::FIELD_IS_TRANSIT_GO, ((count($flights) > 1) ? ($flights - 1) : 0));
        $message->set(FlightMessage::FIELD_IS_TRANSIT_BACK, 0);

        $message->set(FlightMessage::FIELD_ID_PEL1, "TPSW");
        $message->set(FlightMessage::FIELD_JENIS_STRUK, 0);

        if ($this->getOutletId() === "FA22044") {
            $this->response->setStatus("01", "Daftarkan dulu Outlet Anda");
        } else {
            // echo $message->toString();
            // die;
            $this->sendToCore($message);
            //$message->parse('RESERVASI*TPQG*1991736326*8*20170827065101*WEB*FA32224*251386*travel*1968608*BOOKING*5467439914587*TPQG*0*SUB*PLM*09/04/2017*09/04/2017*1*1*1*01***2;QG~ 790~ ~~SUB~09/04/2017 12:45~PLM~09/04/2017 14:30~ SEPARATOR QG~ 790~ ~~SUB~09/04/2017 12:45~PLM~09/04/2017 14:30~ SEPARATOR 0~O~~O~RGFR~~1~X SEPARATOR 2017-09-04 SEPARATOR 2017-09-04;O;630000;14:30;citilink;;;12:45;14:30;QG 790;SUB;PLM****ADT;MRS;VINDA EKA ;YUWANITA;;;::;::081334044321;;;;DAY.TRAVEL88@GMAIL.COM;KTP;ID;ID;;;ID;*CHD;MISS;DAMARA ANGGITA ;YUFELDANTONI;/11/11/2012/;;;;;;;;;ID;ID;;;ID;*INF;MISS;DELISHA ADZKIA ;YUFELDANTONI;/02/04/2016/;;;;;;;;;ID;ID;;;ID****************ADT;MRS;VINDA EKA  YUWANITA;|CHD;MISS;DAMARA ANGGITA  YUFELDANTONI;|INF;MISS;DELISHA ADZKIA  YUFELDANTONI;|***QE59SN;2017-08-26;27-Aug-2017 14:51;330928360227|#ADT;MRS;VINDA EKA  YUWANITA;|CHD;MISS;DAMARA ANGGITA  YUFELDANTONI;|INF;MISS;DELISHA ADZKIA  YUFELDANTONI;|#04-September-2017;QG 790;12:45;14:30;BOOKED;SUB;PLM|#1300000;;1350000;1300000|#330928360227;citilink*QE59SN*330928360227*QG 790*12:45*14:30*******2017-08-26*2017-08-27 14:51:00*****2017-09-04**1350000**1300000*1300000*0*0******************2*BOOKED*1350000*0**0*citilink*795763739*00*SUKSES');
            BookResponseParser::instance()->parse($message)->into($this);
        }
    }

    public static function setToMstrMiss($passenger)
    {
        return str_ireplace(";MR;", ";MSTR;", str_ireplace(";MS;", ";MISS;", $passenger));
    }
}