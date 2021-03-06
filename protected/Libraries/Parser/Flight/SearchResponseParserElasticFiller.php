<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;
use Travel\Libraries\Utility;

class SearchResponseParserElasticFiller extends BaseResponseParser implements ResponseParser
{

    protected $message;
    protected static $airlinelogo = array(
        "JQ" => "http://static.jetstar.com/images/logo_footer.png",
        "TR" => "http://www.tigerair.com/_templates/img/tigerair-id-logo.gif",
        "TN" => "http://static.travel.com/maskapai/logo-trigana.png",
        "MV" => "http://static.travel.com/maskapai/logo-transnusa.png",
        "QG-API" => "http://www.travel.co.id/icon/status-qg.png",
        "SJ" => "http://static.travel.com/maskapai/logo-sriwijaya.png",
        "SY" => "http://static.travel.com/maskapai/logo-sky.png",
        "MZ" => "http://static.travel.com/maskapai/logo-merpati.png",
        "RI" => "http://static.travel.com/maskapai/logo-mandala.png",
        "JT" => "http://static.travel.com/maskapai/logo-lionair.png",
        "KP" => "http://static.travel.com/maskapai/logo-kalstar.png",
        "GA" => "http://static.travel.com/maskapai/logo-garuda.png",
        "XN" => "http://static.travel.com/maskapai/logo-expressair.png",
        "QG" => "http://static.travel.com/maskapai/logo-citilink.png",
        "QZ" => "http://static.travel.com/maskapai/logo-airasia.png",
        "ID" => "https://static.scash.bz/Travel/asset/maskapai/TPID.png",
        "IW" => "https://static.scash.bz/Travel/asset/maskapai/TPIW.png"
    );

    public function into(APIController $apiController)
    {

        $airports = (array) json_decode(Utility::$airport);

        foreach (json_decode($this->message->get(FlightMessage::FIELD_MESSAGE), TRUE) as $key => $value) {
            $flight = new \stdClass();
            $fCount = sizeof(explode(">", $key), FALSE);
            if ($apiController->request->isLowestPrice) {


                foreach ($value as $v) {
                    if (sizeof($v, FALSE) === $fCount) {
                        if (!self::is_nulls($v) && intval($v[0]["price"]) > 0) {
                            for ($index = 1; $index <= $fCount; $index++) {
                                ${"t" . $index} = json_decode(json_encode($v[$index - 1]), FALSE);
                                ${"_classt" . $index} = array();
                                ${"arr_t" . $index} = array();
                                if (!array_key_exists("duration", ${"arr_t" . $index})) {
                                    ${"arr_t" . $index}["duration"] = Utility::getDurations(${"t" . $index}->fromAirport, ${"t" . $index}->toAirport, ${"t" . $index}->fromTime, ${"t" . $index}->toTime);
                                }
                                if (!array_key_exists("arrivaldate", ${"arr_t" . $index})) {
                                    ${"arr_t" . $index}["arrivaldate"] = date('Y-m-d', strtotime(Utility::getArrivalDates(${"t" . $index}->fromAirport, ${"t" . $index}->toAirport, date('d-m-Y', strtotime(${"t" . $index}->date)), ${"t" . $index}->fromTime, ${"t" . $index}->toTime)));
                                }
                                ${"_classt" . $index}[] = (object) array(
                                    "availability" => ${"t" . $index}->availability,
                                    "seatKey" => ${"t" . $index}->seatKey,
                                    "price" => ${"t" . $index}->price,
                                    "depatureTime" => ${"t" . $index}->fromTime,
                                    "arrivalTime" => ${"t" . $index}->toTime,
                                    "class" => ${"t" . $index}->className,
                                    "flightCode" => ${"t" . $index}->flightNumber,
                                    "departure" => ${"t" . $index}->fromAirport,
                                    "departureName" => $airports[${"t" . $index}->fromAirport]->cityName,
                                    "arrival" => ${"t" . $index}->toAirport,
                                    "arrivalName" => $airports[${"t" . $index}->toAirport]->cityName,
                                    "seat" => ${"t" . $index}->seatKey,
                                    "departureDate" => ${"t" . $index}->date,
                                    "arrivalDate" => ${"t" . $index}->date,
                                    "isInternational" => (intval($airports[${"t" . $index}->fromAirport]->isInternational) || intval($airports[${"t" . $index}->toAirport]->isInternational)) ? 1 : 0,
                                    "departureTimeZone" => $airports[${"t" . $index}->fromAirport]->timeZone,
                                    "arrivalTimeZone" => $airports[${"t" . $index}->toAirport]->timeZone,
                                    "departureTimeZoneText" => $airports[${"t" . $index}->fromAirport]->gmtZone,
                                    "arrivalTimeZoneText" => $airports[${"t" . $index}->toAirport]->gmtZone,
                                    "duration" => ${"arr_t" . $index}["duration"],
                                    "arrivaldate" => ${"arr_t" . $index}["arrivaldate"]
                                );
                                $flight->detailTitle[] = (object) array(
                                    "flightIcon" => self::$airlinelogo[substr(${"t" . $index}->flightNumber, 0, 2)],
                                    "flightName" => self::getAirlineName(substr(${"t" . $index}->flightNumber, 0, 2)),
                                    "transitTime" => "0j0m",
                                    "flightCode" => ${"t" . $index}->flightNumber,
                                    "origin" => ${"t" . $index}->fromAirport,
                                    "originName" => $airports[${"t" . $index}->fromAirport]->cityName,
                                    "destination" => ${"t" . $index}->toAirport,
                                    "destinationName" => $airports[${"t" . $index}->toAirport]->cityName,
                                    "depart" => ${"t" . $index}->fromTime,
                                    "arrival" => ${"t" . $index}->toTime,
                                    "departureDate" => ${"t" . $index}->date,
                                    "durationDetail" => ${"arr_t" . $index}["duration"]
                                );
                                $flight->classes[] = ${"_classt" . $index};
                            }
                            $flight->title = $key;
                            $flight->isTransit = (sizeof($flight->detailTitle) === 1) ? false : true;
                            $flight->cityTransit = true;
                            $flight->departureDate = ${"_classt1"}[0]->departureDate;
                            $flight->arrivalDate = date('Y-m-d', strtotime(Utility::getArrivalDates(${"_classt1"}[0]->departure, ${"_classt" . $fCount}[0]->arrival, date('d-m-Y', strtotime(${"_classt1"}[0]->departureDate)), ${"_classt1"}[0]->depatureTime, ${"_classt" . $fCount}[0]->arrivalTime)));
                            $flight->duration = Utility::getDurations(${"_classt1"}[0]->departure, ${"_classt" . $fCount}[0]->arrival, ${"_classt1"}[0]->depatureTime, ${"_classt" . $fCount}[0]->arrivalTime);
                            $flight->airlineCode = "TPJT";
                            $flight->airlineName = self::getAirlineName(substr(${"t1"}->flightNumber, 0, 2));
                            $flight->airlineIcon = self::$airlinelogo[substr(${"t1"}->flightNumber, 0, 2)];
                        }
                    }
                }
            } else {
                foreach ($value as $v) {
                    if (sizeof($v, FALSE) === $fCount) {
                        for ($index = 1; $index <= $fCount; $index++) {
                            ${"t" . $index} = json_decode(json_encode($v[$index - 1]), FALSE);
                            ${"_classt" . $index} = array();
                            ${"arr_t" . $index} = array();
                            if (!array_key_exists("duration", ${"arr_t" . $index})) {
                                ${"arr_t" . $index}["duration"] = Utility::getDurations(${"t" . $index}->fromAirport, ${"t" . $index}->toAirport, ${"t" . $index}->fromTime, ${"t" . $index}->toTime);
                            }
                            if (!array_key_exists("arrivaldate", ${"arr_t" . $index})) {
                                ${"arr_t" . $index}["arrivaldate"] = date('Y-m-d', strtotime(Utility::getArrivalDates(${"t" . $index}->fromAirport, ${"t" . $index}->toAirport, date('d-m-Y', strtotime(${"t" . $index}->date)), ${"t" . $index}->fromTime, ${"t" . $index}->toTime)));
                            }
                            ${"_classt" . $index}[] = (object) array(
                                "availability" => ${"t" . $index}->availability,
                                "seatKey" => ${"t" . $index}->seatKey,
                                "price" => ${"t" . $index}->price,
                                "depatureTime" => ${"t" . $index}->fromTime,
                                "arrivalTime" => ${"t" . $index}->toTime,
                                "class" => ${"t" . $index}->className,
                                "flightCode" => ${"t" . $index}->flightNumber,
                                "departure" => ${"t" . $index}->fromAirport,
                                "departureName" => $airports[${"t" . $index}->fromAirport]->cityName,
                                "arrival" => ${"t" . $index}->toAirport,
                                "arrivalName" => $airports[${"t" . $index}->toAirport]->cityName,
                                "seat" => ${"t" . $index}->seatKey,
                                "departureDate" => ${"t" . $index}->date,
                                "arrivalDate" => ${"t" . $index}->date,
                                "isInternational" => (intval($airports[${"t" . $index}->fromAirport]->isInternational) || intval($airports[${"t" . $index}->toAirport]->isInternational)) ? 1 : 0,
                                "departureTimeZone" => $airports[${"t" . $index}->fromAirport]->timeZone,
                                "arrivalTimeZone" => $airports[${"t" . $index}->toAirport]->timeZone,
                                "departureTimeZoneText" => $airports[${"t" . $index}->fromAirport]->gmtZone,
                                "arrivalTimeZoneText" => $airports[${"t" . $index}->toAirport]->gmtZone,
                                "duration" => ${"arr_t" . $index}["duration"],
                                "arrivaldate" => ${"arr_t" . $index}["arrivaldate"]
                            );
                            $flight->classes[] = ${"_classt" . $index};
                        }

                        for ($index = 1; $index <= $fCount; $index++) {
                            $flight->detailTitle[] = (object) array(
                                "flightIcon" => self::$airlinelogo[substr(${"t" . $index}->flightNumber, 0, 2)],
                                "flightName" => self::getAirlineName(substr(${"t" . $index}->flightNumber, 0, 2)),
                                "transitTime" => "0j0m",
                                "flightCode" => ${"t" . $index}->flightNumber,
                                "origin" => ${"t" . $index}->fromAirport,
                                "originName" => $airports[${"t" . $index}->fromAirport]->cityName,
                                "destination" => ${"t" . $index}->toAirport,
                                "destinationName" => $airports[${"t" . $index}->toAirport]->cityName,
                                "depart" => ${"t" . $index}->fromTime,
                                "arrival" => ${"t" . $index}->toTime,
                                "departureDate" => ${"t" . $index}->date,
                                "durationDetail" => ${"arr_t" . $index}["duration"]
                            );
                            $flight->classes[] = ${"_classt" . $index};
                        }

                        $flight->title = $key;
                        $flight->isTransit = (sizeof($flight->detailTitle) === 1) ? false : true;
                        $flight->cityTransit = true;
                        $flight->departureDate = ${"_classt1"}[0]->departureDate;
                        $flight->arrivalDate = date('Y-m-d', strtotime(Utility::getArrivalDates(${"_classt1"}[0]->departure, ${"_classt" . $fCount}[0]->arrival, date('d-m-Y', strtotime(${"_classt1"}[0]->departureDate)), ${"_classt1"}[0]->depatureTime, ${"_classt" . $fCount}[0]->arrivalTime)));
                        $flight->duration = Utility::getDurations(${"_classt1"}[0]->departure, ${"_classt" . $fCount}[0]->arrival, ${"_classt1"}[0]->depatureTime, ${"_classt" . $fCount}[0]->arrivalTime);
                        $flight->airlineCode = "TPJT";
                        $flight->airlineName = self::getAirlineName(substr(${"t1"}->flightNumber, 0, 2));
                        $flight->airlineIcon = self::$airlinelogo[substr(${"t1"}->flightNumber, 0, 2)];
                    }
                }
            }

            if ($flight != new \stdClass()) {
                $apiController->response->data[] = $flight;
            }
        }
        $apiController->response->mid = 0;
    }

    public static function getAirlineName($data)
    {
        switch ($data) {
            case 'GA':
                return 'Garuda Indonesia';
            case 'SJ':
                return 'Sriwijaya';
            case 'IN':
                return 'Nam Air';
            case 'SJ':
                return 'Sriwijaya';
            case 'JT':
                return 'Lion Air';
            case 'IW':
                return 'Wings Air';
            case 'ID':
                return 'Batik Air';
            case 'QZ':
                return 'AirAsia';
            case 'QG':
                return 'Citilink';
            case 'XN':
                return 'Xpress Air';
            case 'TN':
                return 'Trigana Air';
            case 'KP':
                return 'Kalstar';
            case 'MV':
                return 'Transnusa';
            case 'JQ':
                return 'Jetstar';
            case 'TR':
                return 'Tiger Air';
            default:
                return $data;
        }
    }

    public static function is_nulls($arr)
    {
        foreach ($arr as $value) {
            if (is_null($value)) {
                return true;
            }
        }
        return false;
    }
}