<?php

namespace Travel\Libraries\Models;

class MessageInbox extends BaseModel
{
    public $idMessage;
    public $mid;
    public $step;
    public $sender;
    public $receiver;
    public $content;
    public $idModul;
    public $via;
    public $isSent;
    public $dateCreated;
    public $sentDate;

    public function initialize()
    {
        $this->setSchema('fmss');
    }
}