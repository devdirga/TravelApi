<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Travel\TravelBus;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\TravelBus\GetSeatBookedResponseParser;
use Travel\Libraries\MTI;


/**
 * Description of GetSeatBookedController
 *
 * @author bimasakti
 */
class GetSeatBookedController extends APIController
{

    protected $invoking = "Get Seat Booked TravelBus";

    public function indexAction()
    {

        $this->setMTI(MTI::DATA);
        $this->setProductCode($this->request->produk);

        $message = new TravelBusMessage($this);

        $message->set(TravelBusMessage::FIELD_TANGGAL_BERANGKAT, $this->request->departureDate);
        $message->set(TravelBusMessage::FIELD_TRAVEL_CODE, $this->request->codeAgent);
        $message->set(TravelBusMessage::FIELD_KODE_JADWAL, $this->request->codeJadwal);

        $message->set(TravelBusMessage::FIELD_COMMAND, $this->request->command);

        $this->sendToCore($message);

        GetSeatBookedResponseParser::instance()->parse($message)->into($this);
    }
}