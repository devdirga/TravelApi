<?php

namespace Travel\Hotel;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\HotelMessage;
use Travel\Libraries\Parser\Hotel\DetailResponseParser;
use Travel\Libraries\MTI;

class DetailController extends APIController
{
    protected $invoking = "Detail Hotel";

    public function indexAction()
    {
        $this->setMTI(MTI::HOTELADM);
        $this->setProductCode("RHOTEL");

        $message = new HotelMessage($this);

        $message->set(HotelMessage::FIELD_CMD, "GetHotelDetail");

        $message->set(HotelMessage::FIELD_HOTEL_ID, $this->request->hotelId);

        $message->set(HotelMessage::FIELD_ROOM_COUNT, $this->request->room);
        $message->set(HotelMessage::FIELD_GUEST_COUNT, $this->request->guest);
        $message->set(HotelMessage::FIELD_PERIOD_CHECKIN, $this->request->checkInDate);
        $message->set(HotelMessage::FIELD_PERIOD_CHECKOUT, $this->request->checkOutDate);

        $message->set(HotelMessage::FIELD_HOTEL_BILLER, $this->request->billerId);

        $this->sendToCore($message);

        DetailResponseParser::instance()->parse($message)->into($this);
    }
}