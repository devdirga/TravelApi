<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Models\Outlet;

//use Phalcon\Mvc\Controller;
//use Lcobucci\JWT\Parser;
//use Lcobucci\JWT\Signer\Hmac\Sha256;
//use Lcobucci\JWT\Builder;
//use Travel\Libraries\Response;
//use Travel\Libraries\HttpConnect;

class SignInResponseParser extends BaseResponseParser implements ResponseParser
{

    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(AppMessage::FIELD_STATUS);
        $rd = $this->message->get(AppMessage::FIELD_KETERANGAN);
        if ($rc == "00") {
            $balance = Outlet::take($apiController->getOutletId(), "balance");
            $apiController->response->setDataAsObject();
            $apiController->response->data->balance = $balance;
            $apiController->createToken($this->message->get(AppMessage::FIELD_LOKET_ID), $this->message->get(AppMessage::FIELD_PIN), $this->message->get(AppMessage::FIELD_TOKEN));
            $apiController->response->data->nama_logo = "Travel.bmp";
            //$apiController->response->data->cek_sum_logo = md5_file($apiController->config->path_assets . $apiController->config->logo);
            $apiController->response->data->cek_sum_logo = md5_file("http://api.Travel.co.id/protected/Asset/Img/Travel.bmp");
            $status = $apiController->dbmysql->query("SELECT id_outlet from fcm_user WHERE id_outlet = ?", [$apiController->request->outletId]);
            //$status->setFetchMode(Db::FETCH_OBJ);     
            if (sizeof($status->fetch()) > 0) {
                if (isset($apiController->request->fcmregid)) {
                    $querygcm = $apiController->dbmysql->query("SELECT * from fcm_user WHERE fcmregid = ?", [$apiController->request->fcmregid]);
                    if (sizeof($querygcm->fetch()) == 0) {
                        $apiController->dbmysql->query("UPDATE fcm_user SET fcmregid = ? WHERE id_outlet = ?", [$apiController->request->fcmregid, $apiController->request->outletId]);
                    }
                }
            } else {
                if (isset($apiController->request->fcmregid)) {
                    $apiController->dbmysql->query("INSERT INTO fcm_user (id_outlet,fcmregid) VALUES (?,?)", [$apiController->request->outletId, $apiController->request->fcmregid]);
                }
            }
        }
        $apiController->response->setStatus($rc, $rd);
        $apiController->response->pin = $apiController->request->pin;
        $apiController->response->key = $apiController->request->key;
    }
}