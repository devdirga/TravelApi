<?php

namespace Travel\Libraries\Message;

use Travel\Libraries\APIController;

class TourMessage extends BaseMessage
{
    const FIELD_CITY = 6;
    const FIELD_PROVINCE = 7;
    const FIELD_TOURISM_OBJECT_TYPE = 8;
    const FIELD_DURATION = 9;
    const FIELD_TOURISM_OBJECT = 10;
    const FIELD_TOUR_ID = 11;
    const FIELD_TOUR_DURATION = 12;
    const FIELD_TOUR_NAME = 13;
    const FIELD_TOUR_REGION = 14;
    const FIELD_TOUR_ADDRESS = 15;
    const FIELD_TOUR_TYPE = 16;
    const FIELD_TOUR_THUMBNAIL_URL = 17;
    const FIELD_ITINENARY = 18;
    const FIELD_HOTEL_ACCOMODATION_ID = 19;
    const FIELD_HOTEL_ACCOMODATION_NAME = 20;
    const FIELD_TOUR_START_DATE = 21;
    const FIELD_TOUR_END_DATE = 22;
    const FIELD_TOTAL_PRICE = 23;
    const FIELD_PAX_MIN = 24;
    const FIELD_PAX_MAX = 25;
    const FIELD_PAX_PRICE = 26;
    const FIELD_PAX_NAME = 27;
    const FIELD_PAX_PHONE_NUMBER = 28;
    const FIELD_PAX_COUNT = 29;
    const FIELD_BUYER_NAME = 30;
    const FIELD_BUYER_PHONE_NUMBER = 31;
    const FIELD_INFORMATION_TRANSPORT_TYPE = 32;
    const FIELD_INFORMATION_TRANSPORT_NAME = 33;
    const FIELD_INFORMATION_DEPARTURE_DATE = 34;
    const FIELD_INFORMATION_DEPARTURE_TIME = 35;
    const FIELD_INFORMATION_ARRIVAL_DATE = 36;
    const FIELD_INFORMATION_ARRIVAL_TIME = 37;
    const FIELD_INFORMATION_TRANSPORT_NUMBER = 38;
    const FIELD_INFORMATION_ADDITIONAL = 39;
    const FIELD_BOOK_CODE = 40;
    const FIELD_PAYMENT_CODE = 41;
    const FIELD_REFERENCE_NUMBER = 42;
    const FIELD_TOUR_PROVIDER = 43;
    const FIELD_TOUR_SUMMARY = 44;

    protected $dataLength = 45;

    public function __construct(APIController $controller)
    {
        parent::__construct($controller);

        $this->fillDefault(6);
    }
}