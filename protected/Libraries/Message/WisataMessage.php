<?php

namespace Travel\Libraries\Message;

use Travel\Libraries\APIController;

class WisataMessage extends BaseMessage
{

    const FIELD_ID_PEL1 = 6;
    const FIELD_ID_PEL2 = 7;
    const FIELD_ID_PEL3 = 8;
    const FIELD_NOMINAL = 9;
    const FIELD_NOMINAL_ADMIN = 10;
    const FIELD_LOKET_ID = 11;
    const FIELD_PIN = 12;
    const FIELD_TOKEN = 13;
    const FIELD_BALANCE = 14;
    const FIELD_JENIS_STRUK = 15;
    const FIELD_KODE_BANK = 16;
    const FIELD_KODE_PRODUK_BILLER = 17;
    const FIELD_TRX_ID = 18;
    const FIELD_STATUS = 19;
    const FIELD_KETERANGAN = 20;
    const FIELD_CITY = 21;
    const FIELD_PROVINCE = 22;
    const FIELD_TOURISM_OBJECT_TYPE = 23;
    const FIELD_DURATION = 24;
    const FIELD_TOURISM_OBJECT = 25;
    const FIELD_TOUR_ID = 26;
    const FIELD_TOUR_DURATION = 27;
    const FIELD_TOUR_NAME = 28;
    const FIELD_TOUR_REGION = 29;
    const FIELD_TOUR_ADDRESS = 30;
    const FIELD_TOUR_TYPE = 31;
    const FIELD_TOUR_THUMBNAIL_URL = 32;
    const FIELD_ITINERARY = 33;
    const FIELD_HOTEL_ACCOMODATION_ID = 34;
    const FIELD_HOTEL_ACCOMODATION_NAME = 35;
    const FIELD_TOUR_START_DATE = 36;
    const FIELD_TOUR_END_DATE = 37;
    const FIELD_TOTAL_PRICE = 38;
    const FIELD_PAX_MIN = 39;
    const FIELD_PAX_MAX = 40;
    const FIELD_PAX_PRICE = 41;
    const FIELD_PAX_NAME = 42;
    const FIELD_PAX_PHONE_NUMBER = 43;
    const FIELD_PAX_COUNT = 44;
    const FIELD_BUYER_NAME = 45;
    const FIELD_BUYER_PHONE_NUMBER = 46;
    const FIELD_INFORMATION_TRANSPORT_TYPE = 47;
    const FIELD_INFORMATION_TRANSPORT_NAME = 48;
    const FIELD_INFORMATION_DEPARTURE_DATE = 49;
    const FIELD_INFORMATION_DEPARTURE_TIME = 50;
    const FIELD_INFORMATION_ARRIVAL_DATE = 51;
    const FIELD_INFORMATION_ARRIVAL_TIME = 52;
    const FIELD_INFORMATION_FLIGHT_NUMBER = 53;
    const FIELD_INFORMATION_ADDITIONAL = 54;
    const FIELD_BOOK_CODE = 55;
    const FIELD_PAYMENT_CODE = 56;
    const FIELD_REFERENCE_NUMBER = 57;
    const FIELD_TOUR_PROVIDER = 58;
    const FIELD_TOUR_SUMMARY = 59;
    const FIELD_ADD_ONS = 60;

    protected $dataLength = 61;

    public function __construct(APIController $controller)
    {
        parent::__construct($controller);

        $this->fillDefault(6);

        $this->controller = $controller;

        $this->set(WisataMessage::FIELD_LOKET_ID, $controller->getOutletId());
        $this->set(WisataMessage::FIELD_PIN, $controller->getPin());
        $this->set(WisataMessage::FIELD_TOKEN, $controller->getKey());
    }
}