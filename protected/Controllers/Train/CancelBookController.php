<?php

namespace Travel\Train;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\Parser\Train\CancelBookResponseParser;
use Travel\Libraries\MTI;

class CancelBookController extends APIController
{
    protected $invoking = "Cancel Book Train";

    public function indexAction()
    {
        $this->setMTI(MTI::NKAICAN);
        $this->setProductCode($this->request->productCode);

        $message = new TrainMessage($this);

        $message->set(TrainMessage::FIELD_ID_PEL2, $this->request->bookingCode);
        $message->set(TrainMessage::FIELD_BOOK_CODE, $this->request->bookingCode);
        $message->set(TrainMessage::FIELD_TRX_ID, $this->request->transactionId);

        $message->set(TrainMessage::FIELD_CANCEL_REASON, $this->request->reason);

        $this->sendToCore($message);

        CancelBookResponseParser::instance()->parse($message)->into($this);
    }
}