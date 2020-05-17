<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\RequestKeyResponseParser;

class RequestKeyController extends APIController
{

    protected $invoking = "Request Key App";

    public function indexAction()
    {
        $message = new AppMessage($this);

        if (isset($this->request->pin)) {
            $messageSignIn = new AppMessage($this);
            $messageSignIn->set(AppMessage::FIELD_MTI, 'SIGNON');
            $messageSignIn->set(AppMessage::FIELD_KODE_PRODUK, 'SIGNON');
            $messageSignIn->set(AppMessage::FIELD_LOKET_ID, $this->request->outletId);
            $messageSignIn->set(AppMessage::FIELD_PIN, $this->request->pin);
            $messageSignIn->set(AppMessage::FIELD_VIA, "WEB");
            $messageSignIn->set(AppMessage::FIELD_TOKEN, 'travel');

            $this->sendToCore($messageSignIn);

            if ($messageSignIn->get(AppMessage::FIELD_STATUS) === '00') {
                RequestKeyResponseParser::instance()->parse($message)->into($this);
            } else {
                $this->response->setStatus('02', 'Jika Anda belum menjadi member di Travel, silakan daftar di www.Travel.co.id');
            }
        } else {
            RequestKeyResponseParser::instance()->parse($message)->into($this);
        }
    }
}