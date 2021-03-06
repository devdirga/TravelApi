<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Models\Outlet;
use Travel\Libraries\Models\MessageInbox;


class RequestKeyResponseParser extends BaseResponseParser implements ResponseParser
{

    /**
     * AppMessage.
     * 
     * @var AppMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {

        $phoneNumber = Outlet::take($apiController->request->outletId, "notelpPemilik");
        $rk = $apiController->request->days;

        $messageInbox = new MessageInbox();

        $messageInbox->mid = 1;
        $messageInbox->step = 1;
        $messageInbox->sender = $phoneNumber;
        $messageInbox->receiver = "TOOL_ADMIN";
        $messageInbox->content = $rk;
        $messageInbox->idModul = "MP_Text";
        $messageInbox->via = "SMS";
        $messageInbox->isSent = 0;
        $messageInbox->dateCreated = date("Y-m-d H:i:s");
        $messageInbox->sentDate = date("Y-m-d H:i:s");

        if (trim($phoneNumber) == "") {
            $apiController->response->setStatus("33", "ID Outlet / No. HP Tidak Terdaftar");
        } else {
            //TODO if login success then insert into message inbox

            $start = date("Y-m-d");
            $end = date("Y-m-d");
            $limit = 0;
            if ($rk === "RK1") {
            } else if ($rk === "RK3") {
                $limit = 2;
                $end = date("Y-m-d", strtotime('+2 days'));
            } else if ($rk === "RK5") {
                $limit = 4;
                $end = date("Y-m-d", strtotime('+4 days'));
            }
            if ($rk === "RK7") {
                $limit = 6;
                $end = date("Y-m-d", strtotime('+6 days'));
            }
            $flag = $messageInbox->save();

            if (!$flag) {
                $apiController->response->setStatus("01", "Gagal membuat key.");
            } else {
                $apiController->response->setStatus("00", "Key SMS telah kami kirimkan ke nomor :   " . substr($phoneNumber, 0, 2) . "xxxxxxx" . substr($phoneNumber, -3) . "\nPastikan nomor tersebut berada pada perangkat yang sama agar login diproses secara otomatis");
                $apiController->response->start_date = $start;
                $apiController->response->end_date = $end;
                $apiController->response->day_limit = $limit;
            }
        }
    }
}