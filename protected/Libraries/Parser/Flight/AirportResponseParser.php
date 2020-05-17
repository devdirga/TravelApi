<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class AirportResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Flight Message.
     * 
     * @var FlightMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $airports = $apiController->db->query("select city_code, city_name,city_airp,is_international from mt_destination WHERE is_active = ?", [1])->fetchAll();

        if (count($airports) > 0) {
            foreach ($airports as $airport) {
                $object = new \stdClass();
                if ($airport["city_code"] != "ZZZ") {
                    $object->code = $airport["city_code"];
                    $object->name = $airport["city_name"];
                    $object->bandara = $airport["city_airp"];
                    $object->group = $airport["is_international"] == 1 ? "Internasional" : "Domestik";

                    $apiController->response->data[] = $object;
                }
            }
        } else {
            $apiController->response->setStatus("01", "Airport is empty.");
        }
    }
}