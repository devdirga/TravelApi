<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\ProductCode;
use Phalcon\Db;

use Travel\Libraries\Lists\ListPESAWAT;
use Travel\Libraries\Lists\ListHOTEL;
use Travel\Libraries\Lists\ListKERETA;
use Travel\Libraries\Lists\ListKAPAL;
use Travel\Libraries\Lists\ListWISATA;
use Travel\Libraries\Lists\ListTRAVEL;


class TransactionBookListResponseParser extends BaseResponseParser  implements ResponseParser
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

        //$classname = new \ReflectionClass("List". $apiController->request->product);

        //$apiController->response->data = $classname->initBook($apiController);

        switch ($apiController->request->product) {
            case ProductCode::PESAWAT:
                $result = (new ListPESAWAT())->initBook($apiController);
                break;
            case ProductCode::KERETA:
                $result = (new ListKERETA())->initBook($apiController);
                break;
            case ProductCode::WISATA:
                $result = (new ListWISATA())->initBook($apiController);
                break;
            case ProductCode::HOTEL:
                $result = (new ListHOTEL())->initBook($apiController);
                break;
            case ProductCode::KAPAL:
                $result = (new ListKAPAL())->initBook($apiController);
                break;
            case ProductCode::TRAVEL:
                $result = (new ListTRAVEL())->initBook($apiController);
                break;
            default:
                break;
        }
        $apiController->response->data = $result;
    }
}