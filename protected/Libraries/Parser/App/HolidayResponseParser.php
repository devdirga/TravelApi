<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class HolidayResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Flight Message.
     * 
     * @var FlightMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $holidays = $apiController->db->query("SELECT * FROM setting_menu_reminder_holiday WHERE EXTRACT(YEAR FROM tanggal_libur) = ?", [$apiController->request->year])->fetchAll();

        if (count($holidays) > 0) {
            foreach ($holidays as $holiday) {
                $object = new \stdClass();

                $object->date = $holiday["tanggal_libur"];
                $object->note = $holiday["keterangan"];

                $apiController->response->data[] = $object;
            }
        } else {
            $apiController->response->setStatus("01", "Holiday is empty.");
        }
    }
}