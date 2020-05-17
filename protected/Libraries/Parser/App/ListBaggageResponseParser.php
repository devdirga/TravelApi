<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;

class ListBaggageResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Flight Message.
     * 
     * @var FlightMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $listbaggage = $apiController->db->query("SELECT * FROM additional_baggage where transaksi_id= ?", [$apiController->request->id_transaksi])->fetchAll();

        if (count($listbaggage) > 0) {
            foreach ($listbaggage as $baggage) {
                $object = new \stdClass();
                $object->maskapai = $baggage["maskapai"];
                $object->baggage_key = $baggage["baggage_key"];
                $object->weight = $baggage["weight"];
                $object->price = $baggage["price"];

                $apiController->response->data[] = $object;
            }
        } else {
            $apiController->response->setStatus("01", "List Baggage is empty.");
        }
    }
}