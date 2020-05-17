<?php

namespace Travel\Libraries\Message;

use Travel\Libraries\APIController;

class PelniMessage extends PaymentMessage
{
    const FIELD_OPERATION = 21;
    const FIELD_DATA = 22;

    const FIELD_ORIGINATION = 23;
    const FIELD_ORIGINATION_CALL = 24;

    const FIELD_DESTINATION = 25;
    const FIELD_DESTINATION_CALL = 26;

    const FIELD_DEPARTURE_START_DATE = 27;
    const FIELD_DEPARTURE_END_DATE = 28;

    const FIELD_CLASS = 29;
    const FIELD_SUB_CLASS = 30;
    const FIELD_MALE_PAX = 31;
    const FIELD_FEMALE_PAX = 32;

    const FIELD_IS_PULLING_FARE_DATA = 33;
    const FIELD_IS_PULLING_TELEPHONE_DATA = 34;
    const FIELD_IS_PULLING_LOCATION_DATA = 35;
    const FIELD_IS_PULLING_SEAT_DATA = 36;

    const FIELD_SELLER_BRANCH_CODE = 37;

    const FIELD_DEPARTURE_DATE = 38;
    const FIELD_SHIP_NUMBER = 39;

    const FIELD_PAX_ADULT_TOTAL = 40;
    const FIELD_PAX_CHILD_TOTAL = 41;
    const FIELD_PAX_INFANT_TOTAL = 42;

    const FIELD_ADULT_GENDER = 43;
    const FIELD_CHILD_GENDER = 44;
    const FIELD_INFANT_GENDER = 45;

    const FIELD_ADULT_NAME = 46;
    const FIELD_ADULT_BIRTH_DATE = 47;
    const FIELD_ADULT_IDENTITY_NUMBER = 48;

    const FIELD_CHILD_NAME = 49;
    const FIELD_CHILD_BIRTH_DATE = 50;

    const FIELD_INFANT_NAME = 51;
    const FIELD_INFANT_BIRTH_DATE = 52;

    const FIELD_EMAIL = 53;
    const FIELD_PASSENGER_PHONE_NUMBER = 54;
    const FIELD_PASSENGER_FAMILY = 55;

    const FIELD_PAYMENT_TYPE = 56;

    const FIELD_BOOKING_CODE = 57;
    const FIELD_PAYMENT_CODE = 58;

    const FIELD_ARRIVAL_DATE = 59;
    const FIELD_DEPARTURE_TIME = 60;
    const FIELD_ARRIVAL_TIME = 61;
    const FIELD_SEAT = 62;
    const FIELD_BOOKING_EXPIRED_DATE = 63;
    const FIELD_NORMAL_SALES = 64;
    const FIELD_EXTRA_FEE = 65;
    const FIELD_COMMISSION = 66;
    const FIELD_BOOK_BALANCE = 67;
    const FIELD_DISCOUNT = 68;
    const FIELD_PAY_LIMIT = 69;
    const FIELD_ADDITIONAL_INFO = 70;

    protected $dataLength = 71;

    public function __construct(APIController $controller)
    {
        parent::__construct($controller);

        $this->fillDefault(21);
    }
}