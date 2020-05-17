<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Travel\Umroh;

use Travel\Libraries\APIController;
use Travel\Libraries\HttpConnect;
use Phalcon\Db;

/**
 * Description of UmrohBridgeController
 *
 * @author bimasakti
 */
class UmrohBridgeController extends APIController
{

    protected $invoking = "Umroh Detail App";

    public function indexAction()
    {

        $this->response->setDataAsObject();

        $url = str_replace("-", "/", "-" . $this->request->url);
        $tmpUrl = $this->request->url;

        unset($this->request->token);
        unset($this->request->url);

        if ($tmpUrl === 'other-getallcity') {
            $this->response = $this->getCity();
        } else {
            //error_log(date("h:i:sa") . ' [ ' . $tmpUrl. ' ] before : request => ' . json_encode($this->request));
            $this->response = json_decode(HttpConnect::sendToURL($this->config->umroh->domainUmroh . $this->config->umroh->path . $url, $this->config->umroh->port, json_encode($this->request), "POST")->response);
            //error_log(date("h:i:sa") . ' [ ' . $tmpUrl. ' ] after : response => ' . json_encode($this->response));
        }
    }

    public function getCity()
    {
        return $this->db->fetchAll("SELECT id_kota as id, nama_kota as nama FROM mt_kota ORDER BY nama_kota ASC", Db::FETCH_OBJ);
    }
}