<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\ProductCode;
use Travel\Libraries\Utility;
use Phalcon\Db;

use Travel\Libraries\Lists\ListPESAWAT;
use Travel\Libraries\Lists\ListKERETA;
use Travel\Libraries\Lists\ListHOTEL;
use Travel\Libraries\Lists\ListWISATA;
use Travel\Libraries\Lists\ListKAPAL;
use Travel\Libraries\Lists\ListTRAVEL;

//use Travel\Libraries\Models\Outlet;

class TransactionListResponseParser extends BaseResponseParser implements ResponseParser
{

    /**
     * AppMessage.
     * 
     * @var AppMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {

        $result = array();
        switch ($apiController->request->product) {
            case ProductCode::PESAWAT:
                $result = (new ListPESAWAT())->initPAYMENT($apiController);
                break;
            case ProductCode::KERETA:
                $result = (new ListKERETA())->initPAYMENT($apiController);
                break;
            case ProductCode::WISATA:
                $result = (new ListWISATA())->initPAYMENT($apiController);
                break;
            case ProductCode::HOTEL:
                $result = (new ListHOTEL())->initPAYMENT($apiController);
                break;
            case ProductCode::KAPAL:
                $result = (new ListKAPAL())->initPAYMENT($apiController);
                break;
            case ProductCode::TRAVEL:
                $result = (new ListTRAVEL())->initPAYMENT($apiController);
                break;
            default:
                break;
        }

        // $result = "produk "+$apiController->request->product;
        $apiController->response->data = $result;
    }
}