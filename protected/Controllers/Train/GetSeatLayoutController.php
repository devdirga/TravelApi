<?php

namespace Travel\Train;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\Parser\Train\GetSeatLayoutResponseParser;
use Travel\Libraries\MTI;

class GetSeatLayoutController extends APIController
{
    protected $invoking = "Get Seat Layout Train";

    public function indexAction()
    {
        $this->setMTI(MTI::NKAIMAP);
        $this->setProductCode($this->request->productCode);

        $message = new TrainMessage($this);

        $date = str_replace("-", "", $this->request->date);

        $message->set(TrainMessage::FIELD_ORG, $this->request->origin);
        $message->set(TrainMessage::FIELD_DES, $this->request->destination);
        $message->set(TrainMessage::FIELD_DEP_DATE, $date);
        $message->set(TrainMessage::FIELD_TRAIN_NO, $this->request->trainNumber);

        $this->sendToCore($message);

        GetSeatLayoutResponseParser::instance()->parse($message)->into($this);
    }
}