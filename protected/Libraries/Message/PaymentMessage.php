<?php

namespace Travel\Libraries\Message;

use Travel\Libraries\APIController;

class PaymentMessage extends BaseMessage
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

    protected $dataLength = 21;
    protected $values = array();
    protected $controller;

    public function __construct(APIController $controller)
    {
        parent::__construct($controller);

        $this->fillDefault(6);

        $this->controller = $controller;

        $this->set(PaymentMessage::FIELD_LOKET_ID, $controller->getOutletId());
        $this->set(PaymentMessage::FIELD_PIN, $controller->getPin());
        $this->set(PaymentMessage::FIELD_TOKEN, $controller->getKey());
    }

    public function set($index, $value)
    {
        $this->values[$index] = $value;
    }

    public function get($index)
    {
        return $this->values[$index];
    }

    public function parse($text)
    {
        $this->values = explode("*", $text);
    }

    public function toString()
    {
        return implode("*", $this->values);
    }
}