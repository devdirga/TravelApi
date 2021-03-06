<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;
use Travel\Libraries\HttpConnect;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * Description of FlightElasticController
 *
 * @author bimasakti
 */
class FlightElasticController extends APIController
{

    protected $invoking = "Flight Elastic App";

    public function indexAction()
    {

        $this->response->setDataAsObject();

        $dataResponse = HttpConnect::sendToCore(false, $this->config->flightElastic->host, intval($this->request->port), $this->config->flightElastic->path, $this->request->asterix, 210);

        $this->response->data->result = $dataResponse->response;
    }
}