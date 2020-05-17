<?php

namespace Travel\Libraries\Parser\Pelni;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\PelniMessage;
use Travel\Libraries\APIController;

class SearchResponseParser extends BaseResponseParser implements ResponseParser
{

    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(PelniMessage::FIELD_STATUS);
        $rd = $this->message->get(PelniMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $data = json_decode($this->message->get(PelniMessage::FIELD_DATA));
            //$apiController->response->data = $data->schedule;
            $apiController->response->data = self::parseData($data->schedule);
        }
        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : $rd);
    }

    public static function parseData($data)
    {
        $schedules = $data;
        $schedulesArray = array();
        foreach ($schedules as $schedule) {
            $faresArray = array();
            $fares = $schedule->fares;
            foreach ($fares as $fare) {
                $faresArray[] = (object) array(
                    'SUBCLASS' => $fare->SUBCLASS,
                    'CLASS' => $fare->CLASS,
                    'AVAILABILITY' => ($fare->AVAILABILITY == NULL) ? 0 : (object) array('F' => (string) $fare->AVAILABILITY->F, 'M' => (string) $fare->AVAILABILITY->M),
                    'FARE_DETAIL' => (object) array(
                        'A' => $fare->FARE_DETAIL->A,
                        'I' => $fare->FARE_DETAIL->I,
                        'C' => (object) array(
                            'PORT_PASS' => '0',
                            'ARV_PORT_TRANSPORT_FEE' => '0',
                            'INSURANCE' => '0',
                            'FARE' => '0',
                            'TOTAL' => '0',
                            'DEP_PORT_TRANSPORT_FEE' => '0'
                        )
                    )
                );
            }

            $schedulesArray[] = (object) array(
                'fares' => $faresArray,
                'ORG_CALL' => $schedule->ORG_CALL,
                'DEP_DATE' => $schedule->DEP_DATE,
                'SHIP_NO' => $schedule->SHIP_NO,
                'SHIP_NAME' => $schedule->SHIP_NAME,
                'ARV_TIME' => $schedule->ARV_TIME,
                'ROUTE' => $schedule->ROUTE,
                'ARV_DATE' => $schedule->ARV_DATE,
                'DEP_TIME' => $schedule->DEP_TIME,
                'DES_CALL' => $schedule->DES_CALL
            );
        }
        return $schedulesArray;
    }
}