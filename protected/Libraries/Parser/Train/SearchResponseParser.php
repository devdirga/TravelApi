<?php

namespace Travel\Libraries\Parser\Train;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\APIController;
use Travel\Libraries\Utility;
use DateTime;

class SearchResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Train message response from core.
     * 
     * @var TrainMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(TrainMessage::FIELD_ERR_CODE);
        $rd = $this->message->get(TrainMessage::FIELD_ERR_MSG);

        if ($rc == "00") {
            $trains = json_decode(str_replace(" -,", " \"-\",", $this->message->get(TrainMessage::FIELD_SCHEDULE)));
            $temporary = array();
            foreach ($trains as $train) {
                $object = new \stdClass();

                $departureDate = DateTime::createFromFormat("Ymd", $train[2]);
                $arrivalDate = DateTime::createFromFormat("Ymd", $train[3]);

                $departureTime = DateTime::createFromFormat("Hi", $train[4]);
                $arrivalTime = DateTime::createFromFormat("Hi", $train[5]);

                $object->trainNumber = $train[0];
                $object->trainName = $train[1];
                $object->departureDate = $departureDate->format('Y-m-d');
                $object->arrivalDate = $arrivalDate->format('Y-m-d');
                $object->departureTime = $departureTime->format("H:i");
                $object->arrivalTime = $arrivalTime->format("H:i");
                $object->duration = Utility::getDurations('SUB', 'SUB', $departureTime->format("H:i"), $arrivalTime->format("H:i"));
                $object->seats = array();

                $this->iterateSeats($object->seats, $train[6]);

                if ($this->isShow($departureDate->format('Y-m-d'), $departureTime->format("H:i"))) {
                    //$apiController->response->data[] = $object;
                    $temporary[] = $object;
                }
            }

            $avail = array();
            $notavail = array();
            foreach ($temporary as $rows) {
                $subavail = array();
                $subnotavail = array();
                $tmp = $rows->seats;
                foreach ($tmp as $v) {
                    if (intval($v->availability) > 0) {
                        $subavail[] = $v;
                    } else {
                        $subnotavail[] = $v;
                    }
                }
                if (sizeof($subavail) > 0) {
                    $avail[] = (object) array(
                        "trainNumber" => $rows->trainNumber,
                        "trainName" => $rows->trainName,
                        "departureDate" => $rows->departureDate,
                        "arrivalDate" => $rows->arrivalDate,
                        "departureTime" => $rows->departureTime,
                        "arrivalTime" => $rows->arrivalTime,
                        "duration" => $rows->duration,
                        "seats" => $subavail
                    );
                }
                if (sizeof($subnotavail) > 0) {
                    $notavail[] = (object) array(
                        "trainNumber" => $rows->trainNumber,
                        "trainName" => $rows->trainName,
                        "departureDate" => $rows->departureDate,
                        "arrivalDate" => $rows->arrivalDate,
                        "departureTime" => $rows->departureTime,
                        "arrivalTime" => $rows->arrivalTime,
                        "duration" => $rows->duration,
                        "seats" => $subnotavail
                    );
                }
            }

            usort($avail, function ($a, $b) {
                return (strtotime($a->departureDate . " " . $a->departureTime . ":00") <= strtotime($b->departureDate . " " . $b->departureTime . ":00")) ? -1 : 1;
            });

            usort($notavail, function ($a, $b) {
                return (strtotime($a->departureDate . " " . $a->departureTime . ":00") >= strtotime($b->departureDate . " " . $b->departureTime . ":00")) ? -1 : 1;
            });

            $ret = array_merge($avail, $notavail);

            $apiController->response->data = $ret;
        }

        $apiController->response->setStatus($rc == "0" ? "00" : $rc, $rc == "00" ? "Success" : $rd);
    }

    private function iterateSeats(&$schedule, $seats)
    {
        foreach ($seats as $seat) {
            $object = new \stdClass();

            $object->class = $seat[0];
            $object->availability = $seat[1];
            $object->grade = $seat[2];
            $object->priceAdult = $seat[3];
            $object->priceChild = $seat[4] == "-" ? 0 : $seat[4];
            $object->priceInfant = $seat[5] == "-" ? 0 : $seat[5];

            $schedule[] = $object;
        }
    }

    private function getMIN_HOURS_TO_SHOW_SCHEDULE()
    {
        $url = "http://mp.jadipergi.com/time_limit_search_kai/KAI.conf";
        $conf = file_get_contents($url, true);
        $arr_conf = explode("\n", $conf);
        $setting = 3;
        foreach ($arr_conf as $value) {
            if (strpos($value, "search_time_limit") !== FALSE) {
                $temp_search_time_limit = explode("=", $value);
                $setting = intval($temp_search_time_limit[1]);
            }
        }
        return $setting;
    }

    private function isShow($depDate, $depTime)
    {

        $url = "http://mp.jadipergi.com/time_limit_search_kai/KAI.conf";
        $conf = file_get_contents($url, true);
        $arr_conf = explode("\n", $conf);
        $setting = 3;

        foreach ($arr_conf as $value) {
            if (strpos($value, "search_time_limit") !== FALSE) {
                $temp_search_time_limit = explode("=", $value);
                $setting = intval($temp_search_time_limit[1]);
            }
        }

        $tgl_berangkat_param_data = date("d-m-Y", strtotime($depDate)) . ' ' . $depTime . ":00";
        $tgl_berangkat_param = date('Y-m-d H.i.s', strtotime($tgl_berangkat_param_data));
        $tgl_sekarang_param = date('Y-m-d H.i.s');
        $start = strtotime($tgl_berangkat_param);
        $end = strtotime($tgl_sekarang_param);
        $selisih_jam = ceil((($start - $end) / 86400) * 24);

        if (intval($selisih_jam) > intval($setting)) {
            return true;
        } else {
            return false;
        }
    }
}