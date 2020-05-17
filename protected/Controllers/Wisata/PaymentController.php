<?php

namespace Travel\Wisata;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\WisataMessage;
use Travel\Libraries\Parser\Wisata\PaymentResponseParser;
use Travel\Libraries\Utility;
use Travel\Libraries\MTI;

class PaymentController extends APIController
{
    protected $invoking = "Payment Wisata";

    public function indexAction()
    {

        $this->setMTI(MTI::WISATAPAY);
        $this->setProductCode("TOURNEW");

        $message = new WisataMessage($this);

        $message->set(WisataMessage::FIELD_ID_PEL1, $this->request->bookCode);
        $message->set(WisataMessage::FIELD_ID_PEL2, $this->request->bookCode);
        $message->set(WisataMessage::FIELD_BOOK_CODE, $this->request->bookCode);
        $message->set(WisataMessage::FIELD_PAYMENT_CODE, $this->request->bookCode);
        $message->set(WisataMessage::FIELD_TRX_ID, $this->request->inquiryTrxId);

        $this->request->simulateSuccess = $this->config->environment;

        if (!$this->request->simulateSuccess) {
            if (!Utility::isTesterOutlet($this->getOutletId())) {
                $this->sendToCore($message);
            } else {
                $message->parse('TOURPAY*TOURNEW*2878025808*7*20180912110727*WEB*0877747936019*C66FR7**1000000*0*FA57071*------**24982311*1**TOURNEW*1113760237*00*SUCCESS**28**1**2873*1*OPEN TRIP BROMO START MALANG *******-1*2018-09-16*2018-09-17*1000000***250000*Puji Astuti*0877747936019*4*Puji Astuti*0877747936019********930000*C66FR7*C66FR7*BMS1114887213***');
            }
        }

        PaymentResponseParser::instance()->parse($message)->into($this);
    }
}