<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Travel\TravelBus;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TravelBusMessage;
use Travel\Libraries\Parser\TravelBus\GetVehicleResponseParser;
use Travel\Libraries\MTI;
use Phalcon\Db;

/**
 * Description of GetVehicleController
 *
 * @author bimasakti
 */
class GetVehicleController extends APIController
{

    protected $invoking = "Get Vehicle TravelBus";

    public function indexAction()
    {

        $this->setMTI(MTI::DATA);
        $this->setProductCode($this->request->produk);

        $message = new TravelBusMessage($this);

        $message->set(TravelBusMessage::FIELD_TRAVEL_CODE, $this->request->codeAgent);
        $message->set(TravelBusMessage::FIELD_LAYOUT_KURSI, $this->request->layoutKursi);
        $message->set(TravelBusMessage::FIELD_COMMAND, $this->request->command);

        $this->sendToCore($message);

        GetVehicleResponseParser::instance()->parse($message)->into($this);
    }
}