<?php

namespace Travel\Train;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\Parser\Train\SearchResponseParser;
use Travel\Libraries\MTI;

class SearchController extends APIController
{
    protected $invoking = "Search Train";

    public function indexAction()
    {
        $this->setMTI(MTI::NKAISCH);
        $this->setProductCode($this->request->productCode);

        $message = new TrainMessage($this);

        $date = str_replace("-", "", $this->request->date);

        $message->set(TrainMessage::FIELD_ORG, $this->request->origin);
        $message->set(TrainMessage::FIELD_DES, $this->request->destination);
        $message->set(TrainMessage::FIELD_DEP_DATE, $date);
        $message->set(TrainMessage::FIELD_ARV_DATE, $date);

        $this->sendToCore($message);

        SearchResponseParser::instance()->parse($message)->into($this);
    }
}