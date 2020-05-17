<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Models\Outlet;

class AccountResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * AppMessage.
     * 
     * @var AppMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $outlet = Outlet::take($apiController->getOutletId());

        if ($outlet != null) {
            $apiController->response->setDataAsObject();

            unset($outlet->pin);
            unset($outlet->token);

            $apiController->response->data = $outlet;
            $apiController->response->data->pin = $apiController->getPin();
        } else {
            $apiController->response->setStatus("01", "Outlet is not found.");
        }
    }
}