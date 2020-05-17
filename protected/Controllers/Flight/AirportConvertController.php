<?php

namespace Travel\Flight;

use Travel\Libraries\APIController;

class AirportConvertController extends APIController
{
    protected $invoking = "AirportConvertFlight";

    public function indexAction()
    {
        $this->response->data = json_decode(file_get_contents(str_ireplace("Controllers/Flight", "Libraries", dirname(__FILE__)) . "/airportcode.json"));
    }

    public function addAction()
    {
        if (
            isset($this->request->airline) && isset($this->request->airport) && isset($this->request->convertion) &&
            !empty($this->request->airline) && !empty($this->request->airport) && !empty($this->request->convertion)
        ) {
            $obj = json_decode(file_get_contents(str_ireplace("Controllers/Flight", "Libraries", dirname(__FILE__)) . "/airportcode.json"));
            if (isset($obj->{$this->request->airline})) {
                $airline = $obj->{$this->request->airline};
                if (!array_key_exists($this->request->airport, $airline)) {
                    $airline->{$this->request->airport} = $this->request->convertion;
                } else {
                    $this->response->setStatus("01", "Alredy exist");
                }
            } else {
                $obj->{$this->request->airline} = array($this->request->airport => $this->request->convertion);
            }
            file_put_contents(str_ireplace("Controllers/Flight", "Libraries", dirname(__FILE__)) . "/airportcode.json", json_encode($obj));
        } else {
            $this->response->setStatus("01", "Data request invalid");
        }
    }

    public function deleteAction()
    {
        if (
            isset($this->request->airline) && isset($this->request->airport) &&
            !empty($this->request->airline) && !empty($this->request->airport)
        ) {
            $obj = json_decode(file_get_contents(str_ireplace("Controllers/Flight", "Libraries", dirname(__FILE__)) . "/airportcode.json"));
            if (isset($obj->{$this->request->airline})) {
                $airline = $obj->{$this->request->airline};
                if (!array_key_exists($this->request->airport, $airline)) {
                    $this->response->setStatus("01", "Data not exist");
                } else {
                    unset($obj->{$this->request->airline}->{$this->request->airport});
                    $this->response->setStatus("00", "Succes delete data");
                }
            } else {
                $this->response->setStatus("01", "Data not exist");
            }
            file_put_contents(str_ireplace("Controllers/Flight", "Libraries", dirname(__FILE__)) . "/airportcode.json", json_encode($obj));
        } else {
            $this->response->setStatus("01", "Data request invalid");
        }
    }
}