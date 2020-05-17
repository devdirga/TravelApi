<?php

namespace Travel\Jadipergi;

use Travel\Libraries\APIController;


class SignInController extends APIController
{

    protected $invoking = "Sign In Jadipergi";

    protected $method = "login";

    public function indexAction()
    {

        $result = $this->send_request_oauth(
            array(
                'phone' => $this->request->phone,
                'email' => $this->request->email,
                'pin' => $this->request->pin,
                'app' => $this->request->app,
                'origin_agent' => $this->request->origin_agent,
            ),
            $this->method
        );

        print_r($result);
    }

    protected function send_request_oauth($data, $method)
    {
        $URL = "https://single.bm.co.id" . "/" . $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'BOS-Token: ⁠⁠⁠0a07d32c64888a1a65f58f4e8302a8db',
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        //$this->appendfile($URL);
        //$this->appendfile($data);
        //$this->appendfile($result);
        return $result;
    }
}