<?php

/*
 * @Message
 * 
 * 
 */

namespace Travel\Libraries\Message;

use Travel\Libraries\APIController;
use Travel\Libraries\Utility;

class BaseMessage
{
    const FIELD_MTI = 0;
    const FIELD_KODE_PRODUK = 1;
    const FIELD_MID = 2;
    const FIELD_STEP = 3;
    const FIELD_DATETIME = 4;
    const FIELD_VIA = 5;

    protected $dataLength = 6;
    protected $values = array();
    protected $controller;

    public function __construct(APIController $controller)
    {
        $this->controller = $controller;

        $this->values[BaseMessage::FIELD_MTI] = $controller->getMTI();
        $this->values[BaseMessage::FIELD_KODE_PRODUK] = $controller->getProductCode();
        $this->values[BaseMessage::FIELD_MID] = Utility::getMid($controller);
        $this->values[BaseMessage::FIELD_STEP] = 1;
        $this->values[BaseMessage::FIELD_DATETIME] = date("YmdHis");
        $this->values[BaseMessage::FIELD_VIA] = "MOBILE";
    }

    public function set($index, $value)
    {
        $this->values[$index] = $value;
    }

    public function get($index)
    {
        return isset($this->values[$index]) ? $this->values[$index] : "";
    }

    public function parse($object)
    {
        $this->values = explode("*", $object);
    }

    public function toString()
    {
        return implode("*", $this->values);
    }

    protected function fillDefault($startFrom)
    {
        for ($i = $startFrom; $i < $this->dataLength; $i++) {
            if (!isset($this->values[$i])) {
                $this->values[$i] = "";
            }
        }
    }
}