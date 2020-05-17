<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\Parser\Flight\PaymentResponseParser;
use Travel\Libraries\Utility;
use Travel\Libraries\MTI;
use Phalcon\Db;

class PaymentController extends APIController
{
    protected $invoking = "Payment Flight";

    public function indexAction()
    {
        $this->setMTI(MTI::PAYMENT);
        $this->setProductCode($this->request->airline);

        $message = new FlightMessage($this);

        $o = $this->db->query("SELECT * FROM transaksi WHERE id_transaksi = ?", [$this->request->transactionId]);
        $o->setFetchMode(Db::FETCH_OBJ);
        $t = $o->fetch();

        $message->set(FlightMessage::FIELD_FLIGHT_STEP, "ISSUE");
        $message->set(FlightMessage::FIELD_INDEX, $this->request->paymentCode);
        $message->set(FlightMessage::FIELD_MASKAPAI, $this->request->airline);
        $message->set(FlightMessage::FIELD_RUTE, "0");
        $message->set(FlightMessage::FIELD_PROCESS, "01");

        //$message->set(FlightMessage::FIELD_DATE_DEPARTURE, $this->request->date_departure);
        //$message->set(FlightMessage::FIELD_DATE_ARRIVAL, $this->request->date_arrival);

        $message->set(FlightMessage::FIELD_CLASSNAME2, $this->request->classname2);
        $message->set(FlightMessage::FIELD_CLASSNAME3, $this->request->classname3);
        $message->set(FlightMessage::FIELD_CLASSNAME4, $this->request->classname4);

        $message->set(FlightMessage::FIELD_COUNT_ADULT, $t->bill_info18);
        $message->set(FlightMessage::FIELD_COUNT_CHILD, $t->bill_info19);
        $message->set(FlightMessage::FIELD_COUNT_BABY, $t->bill_info20);

        $message->set(FlightMessage::FIELD_BOOKING_CODE, $this->request->bookingCode);
        $message->set(FlightMessage::FIELD_PAYMENT_CODE, $this->request->paymentCode);

        $message->set(FlightMessage::FIELD_PAX_PAID, $t->nominal);
        $message->set(FlightMessage::FIELD_NTA, $t->bill_info67);
        $message->set(FlightMessage::FIELD_AGENT_PAID, $t->hpp);

        $message->set(FlightMessage::FIELD_IS_TRANSIT_GO, 0);
        $message->set(FlightMessage::FIELD_IS_TRANSIT_BACK, 0);
        $message->set(FlightMessage::FIELD_ID_PEL1, "TPSW");

        $message->set(FlightMessage::FIELD_NOMINAL, $t->nominal);
        $message->set(FlightMessage::FIELD_NOMINAL_ADMIN, $t->nominal_admin);
        $message->set(FlightMessage::FIELD_JENIS_STRUK, "0");
        $message->set(FlightMessage::FIELD_TRX_ID, $this->request->transactionId);

        $this->request->simulateSuccess = $this->config->environment;

        if (!$this->request->simulateSuccess) {
            if (!Utility::isTesterOutlet($this->getOutletId())) {
                $this->sendToCore($message);
            } else {
                $message->parse('PAYMENT*TPJT*3626190636*10*20190730082212*ADMIN*FA110260*------*------*3374323*ISSUE*20190730081822*TPJT*0*BPN*UPG*03-Agu-2019*08-Mar-2019*1*0*0*03*******ADT;MR;ABDUL;HALIL GADDONG;;;::623187762;::082153542184;;;;cybereastborneo@gmail.com;KTP;ID;ID;**********************EYKOHB**JT675*14:40*15:50*******30-Jul-2019*30-Jul-2019 12:18*2019-07-30*bimasaktiduapuluh*EYKOHB**2019-08-03**773900*2;10000*746300*746300*0*0**********ADT;MR;ABDUL;HALIL GADDONG;;;::623187762;::082153542184;;;;cybereastborneo@gmail.com;KTP;ID;ID;*******EYKOHB***773900*25000*2**lion*1458640948*00*SUCCESS AUTOMATIC');
            }
        }

        //$message->parse('PAYMENT*TPJT*1929492593*10*20170731184317*ADMIN*FA66368*------*------*1403110*ISSUE*1501475295393*TPJT*0*CGK*MNA*02-Agu-2017*08-Feb-2017*1*0*0*03*******ADT;MR;OKTAVIYANTO;MALAA;;;::082291712473;::082291712473;;;;jerryvernando@gmail.com;1;ID**********************YTVBCK*-*ID6278*05:00*09:20*10:00*11:05*****31-Jul-2017*31-Jul-2017 21:28*2017-07-31*bimasaktiduadua*YTVBCK**2017-08-02**1787000**1699000*1699000*1*0*MDC**IW1162**02-Agustus-2017*****ADT;MR;OKTAVIYANTO;MALAA;;;::082291712473;::082291712473;;;;jerryvernando@gmail.com;1;ID*******YTVBCK*-**1787000*100000*1**lion*927112457*00*SUCCESS AUTOMATIC');

        PaymentResponseParser::instance()->parse($message)->into($this);
    }
}