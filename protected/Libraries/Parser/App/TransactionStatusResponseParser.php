<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Lists\ListPESAWAT;
use Travel\Libraries\Lists\ListHOTEL;
use Travel\Libraries\Lists\ListKERETA;
use Travel\Libraries\Lists\ListWISATA;
use Travel\Libraries\Lists\ListTRAVEL;
use Travel\Libraries\Lists\ListKAPAL;
use Travel\Libraries\ProductCode;


class   TransactionStatusResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * AppMessage.
     * 
     * @var AppMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {


        $apiController->response->setDataAsObject();

        switch ($apiController->request->product) {
            case ProductCode::PESAWAT:
                $additionaldata = (new ListPESAWAT())->initStatus($apiController->request->bookCode);
                break;
            case ProductCode::KERETA:

                $additionaldata = (new ListKERETA())->initStatus($apiController->request->bookCode);
                break;
            case ProductCode::HOTEL:
                $additionaldata = (new ListHOTEL())->initStatus($apiController->request->bookCode);
                break;
            case ProductCode::KAPAL:
                $additionaldata = (new ListKAPAL())->initStatus($apiController->request->bookCode);
                break;
            case ProductCode::WISATA:
                $additionaldata = (new ListWISATA())->initStatus($apiController->request->bookCode);
                break;
            case ProductCode::TRAVEL:
                $additionaldata = (new ListTRAVEL())->initStatus($apiController->request->bookCode);
                break;
            default:
                $apiController->response->setStatus("01", "Product Code Salah");
                $additionaldata = new \stdClass;
                break;
        }

        $apiController->response->data = $additionaldata;
    }
}