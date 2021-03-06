<?php

namespace Travel\Libraries\Parser\Hotel;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\HotelMessage;
use Travel\Libraries\APIController;

class SearchResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Hotel Message.
     * 
     * @var HotelMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(HotelMessage::FIELD_STATUS);
        $rd = $this->message->get(HotelMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $data = json_decode($this->message->get(HotelMessage::FIELD_HOTEL_DATA));

            //$apiController->response->data = $data->hotel;

            $ret = $data->hotel;

            usort($ret, function ($a, $b) {
                return ($a->roomCateg[0]->roomType->totalPrice <= $b->roomCateg[0]->roomType->totalPrice) ? -1 : 1;
            });

            $responseData = array();

            foreach ($ret as $value) {
                $responseData[] = (object) array(
                    'roomCateg' =>  $value->roomCateg,
                    'hotelId' => $value->hotelId,
                    'hotelName' => $value->hotelName,
                    'hotelAddress' => $value->hotelAddress,
                    'hotelImage' => ($value->hotelImage !== '') ? $value->hotelImage : 'https://static.scash.bz/jadipergi/img/placeholder_image.png',
                    'rating' => $value->rating,
                    'currency' => $value->currency,
                    'internalCode' => $value->internalCode,
                    'idBillerHotel' => $value->idBillerHotel,
                    'idHotelFp' => $value->idHotelFp,
                    'avail' => $value->avail
                );
            }

            $apiController->response->data = $responseData;

            //'hotelImage' => ($value->hotelImage !== '') ? $value->hotelImage : $value->hotelImage ,
            //'roomImage' => $value->roomCateg[0]->roomType->roomImage,

        }

        $apiController->response->mid = $this->message->get(HotelMessage::FIELD_MID);
        $apiController->response->setStatus($rc, $rc == "00" ? "Sukses" : $rd);
    }
}