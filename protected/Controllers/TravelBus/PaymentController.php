<?php

namespace Travel\TravelBus;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\TravelBus\PaymentResponseParser;
use Travel\Libraries\Utility;
use Travel\Libraries\MTI;

class PaymentController extends APIController
{
    protected $invoking = "Payment TravelBus";

    public function indexAction()
    {
        $this->setMTI(MTI::BAYAR);
        $this->setProductCode($this->request->produk);

        $message = new TravelBusMessage($this);

        $message->set(TravelBusMessage::FIELD_COMMAND, $this->request->command);
        $message->set(TravelBusMessage::FIELD_KODE_BOOKING, $this->request->kodeBook);
        $message->set(TravelBusMessage::FIELD_KODE_PEMBAYARAN, $this->request->kodePembayan);
        $message->set(TravelBusMessage::FIELD_NOMINAL, $this->request->nominal);
        $message->set(TravelBusMessage::FIELD_NOMINAL_ADMIN, $this->request->nominalAdmin);
        $message->set(TravelBusMessage::FIELD_TRANSACTION_ID, $this->request->idTransaksi);

        $this->request->simulateSuccess = $this->config->environment;

        if (!$this->request->simulateSuccess) {
            if (!Utility::isTesterOutlet($this->getOutletId())) {
                $this->sendToCore($message);
            }
        }

        PaymentResponseParser::instance()->parse($message)->into($this);
    }
}