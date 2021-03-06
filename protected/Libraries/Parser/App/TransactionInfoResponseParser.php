<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaselistResponseParser;
use Travel\Libraries\Parser\ListResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Lists\ListPESAWAT;
use Travel\Libraries\Lists\ListHOTEL;
use Travel\Libraries\Lists\ListKERETA;
use Travel\Libraries\Lists\ListWISATA;
use Travel\Libraries\Lists\ListTRAVEL;
use Travel\Libraries\Lists\ListKAPAL;
use Travel\Libraries\ProductCode;
use Travel\Libraries\Utility;

class   TransactionInfoResponseParser extends BaselistResponseParser implements ListResponseParser
{
    /**
     * AppMessage.
     * 
     * @var AppMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {

        //$apiController->response->setDataAsObject();

        //$additionaldata =  (new \ReflectionClass("List" . $this->product))->getMethod('init')->invokeArgs(null,array(implode("#", $this->datatransaksi)));
        switch ($this->product) {
            case ProductCode::PESAWAT:
                //$additionaldata = (new ListPESAWAT())->initObject($this->data_transaction);
                $additionaldata = (new ListPESAWAT())->initObject($apiController, $this->data_transaction);
                break;
            case ProductCode::KERETA:
                $additionaldata = (new ListKERETA())->initObject($apiController, $this->data_transaction);
                break;
            case ProductCode::HOTEL:
                $additionaldata = (new ListHOTEL())->initObject($this->data_transaction);
                break;
            case ProductCode::KAPAL:
                $additionaldata = (new ListKAPAL())->initObject($this->data_transaction);
                break;
            case ProductCode::WISATA:
                $additionaldata = (new ListWISATA())->initObject($this->data_transaction);
                break;
            case ProductCode::TRAVEL:
                $additionaldata = (new ListTRAVEL())->initObject($this->data_transaction);
                break;
        }
        if (sizeof($this->data_base) > 0) {
            $apiController->response->komisi = Utility::getKomisi($apiController, $this->data_transaction->id_transaksi);
            $apiController->response->url_etiket = $additionaldata->url_etiket;
            $apiController->response->url_struk = $additionaldata->url_struk;
            $apiController->response->url_image = $additionaldata->url_image;
            $apiController->response->transaction_id = $this->data_transaction->id_transaksi;
            $apiController->response->product = $this->product;
            //$apiController->response->time_limit = (($this->product === ProductCode::PESAWAT )?$this->data_transaction->bill_info31 : \date($this->data_transaction->time_request,\strtotime(\date("Y-m-d H:i:s")." + 2 hours")));
            $default_timeLimit = date('Y-m-d H:i:s', strtotime('+2 hour', strtotime($this->data_transaction->time_request)));
            if ($this->product == "KERETA") {
                $default_timeLimit = date('Y-m-d H:i:s', strtotime('+10 minute', strtotime($this->data_transaction->time_request)));
            }
            $apiController->response->time_limit = (($this->product === ProductCode::PESAWAT) ? $this->data_transaction->bill_info31 : $default_timeLimit);
            $apiController->response->price = intval($this->data_transaction->nominal) + intval($this->data_transaction->nominal_admin);
            $apiController->response->data = array("base" => $this->data_base, "additional_data" => $additionaldata->additional);
        }
    }
}