<?php

namespace Travel\Libraries\Message;

use Travel\Libraries\APIController;

class FlightMessage extends BaseMessage
{
    const FIELD_LOKET_ID = 6;
    const FIELD_PIN = 7;
    const FIELD_TOKEN = 8;
    const FIELD_BALANCE = 9;
    const FIELD_FLIGHT_STEP = 10;

    const FIELD_INDEX = 11;
    const FIELD_MASKAPAI = 12;
    const FIELD_RUTE = 13;
    const FIELD_CITY_ORIGIN = 14;
    const FIELD_CITY_DESTINATION = 15;
    const FIELD_DATE_DEPARTURE = 16;
    const FIELD_DATE_ARRIVAL = 17;
    const FIELD_COUNT_ADULT = 18;
    const FIELD_COUNT_CHILD = 19;
    const FIELD_COUNT_BABY = 20;

    const FIELD_PROCESS = 21;
    const FIELD_CAPTCHA = 22;
    const FIELD_FILE_NAME = 23;
    const FIELD_CLASSNAME1 = 24;
    const FIELD_CLASSNAME2 = 25;
    const FIELD_CLASSNAME3 = 26;
    const FIELD_CLASSNAME4 = 27;

    const FIELD_ADULT1 = 28;
    const FIELD_CHILD1  = 29;
    const FIELD_INFAN1 = 30;

    const FIELD_ADULT2 = 31;
    const FIELD_CHILD2  = 32;
    const FIELD_INFAN2 = 33;

    const FIELD_ADULT3 = 34;
    const FIELD_CHILD3  = 35;
    const FIELD_INFAN3 = 36;

    const FIELD_ADULT4 = 37;
    const FIELD_CHILD4  = 38;
    const FIELD_INFAN4 = 39;

    const FIELD_ADULT5 = 40;
    const FIELD_CHILD5  = 41;
    const FIELD_INFAN5 = 42;

    const FIELD_ADULT6 = 43;
    const FIELD_CHILD6  = 44;
    const FIELD_INFAN6 = 45;

    const FIELD_ADULT7 = 46;
    const FIELD_CHILD7  = 47;
    const FIELD_INFAN7 = 48;

    const FIELD_MESSAGE = 49;
    const FIELD_BOOKING_CODE = 50;
    const FIELD_PAYMENT_CODE = 51;
    const FIELD_CODE_FLIGHT = 52;
    const FIELD_DEPT_TIME = 53;
    const FIELD_ARR_TIME = 54;
    const FIELD_DEPT_TIME2 = 55;
    const FIELD_ARR_TIME2 = 56;
    const FIELD_DEPT_TIME_RETURN = 57;
    const FIELD_ARR_TIME_RETURN = 58;
    const FIELD_DEPT_TIME_RETURN2 = 59;
    const FIELD_ARR_TIME_RETURN2 = 60;

    const FIELD_RESERVATION_DATE = 61;

    const FIELD_TIMELIMIT = 62;
    const FIELD_ISSUED_DATE = 63;
    const FIELD_ISSUED_SIGN = 64;
    const FIELD_ISSUED_CODE = 65;
    const FIELD_CODE_FLIGHT_BACK = 66;

    const FIELD_FLIGHT_INFO_GO = 67;
    const FIELD_FLIGHT_INFO_BACK = 68;
    const FIELD_PAX_PAID = 69;
    const FIELD_OTHER_PAID = 70;
    const FIELD_NTA = 71;
    const FIELD_AGENT_PAID = 72;

    const FIELD_IS_TRANSIT_GO = 73;
    const FIELD_IS_TRANSIT_BACK = 74;
    const FIELD_TRANSIT_VIA_GO = 75;
    const FIELD_TRANSIT_VIA_BACK = 76;
    const FIELD_CODE_FLIGHT_TRANSIT_GO = 77;
    const FIELD_CODE_FLIGHT_TRANSIT_BACK = 78;
    const FIELD_DATE_TRANSIT_GO = 79;
    const FIELD_DATE_TRANSIT_BACK = 80;

    const FIELD_TRANSIT_VIA_GO2 = 81;
    const FIELD_TRANSIT_VIA_BACK2 = 82;
    const FIELD_CODE_FLIGHT_TRANSIT_GO2 = 83;
    const FIELD_CODE_FLIGHT_TRANSIT_BACK2 = 84;
    const FIELD_DATE_TRANSIT_GO2 = 85;
    const FIELD_DATE_TRANSIT_BACK2 = 86;
    const FIELD_DEPT_TIME3 = 87;
    const FIELD_ARR_TIME3 = 88;
    const FIELD_DEPT_TIME_RETURN3 = 89;
    const FIELD_ARR_TIME_RETURN3 = 90;

    const FIELD_ID_PEL1 = 91;
    const FIELD_ID_PEL2 = 92;
    const FIELD_ID_PEL3 = 93;
    const FIELD_NOMINAL = 94;
    const FIELD_NOMINAL_ADMIN = 95;
    const FIELD_JENIS_STRUK = 96;
    const FIELD_KODE_BANK = 97;
    const FIELD_KODE_PRODUK_BILLER = 98;
    const FIELD_TRX_ID = 99;
    const FIELD_STATUS = 100;
    const FIELD_KETERANGAN = 101;

    protected $dataLength = 102;

    public function __construct(APIController $controller)
    {
        parent::__construct($controller);

        $this->fillDefault(6);

        $this->values[FlightMessage::FIELD_LOKET_ID] = $controller->getOutletId();
        $this->values[FlightMessage::FIELD_PIN] = $controller->getPin();
        $this->values[FlightMessage::FIELD_TOKEN] = $controller->getKey();
    }
}