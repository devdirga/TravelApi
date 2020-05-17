<?php

namespace Travel\App;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Parser\App\SignInResponseParser;
use Travel\Libraries\Crypt\FmssRequestDecryptor;
use Travel\Libraries\Utility;
use Phalcon\Db;

class SignInController extends APIController
{
    protected $invoking = "Sign In App";

    public function indexAction()
    {

        $this->setMTI("SIGNON");
        $this->setProductCode("SIGNON");

        $this->setOutletId($this->request->outletId);
        $this->setPin($this->request->pin);
        $this->setKey($this->request->key);

        $message = new AppMessage($this);

        $message->set(AppMessage::FIELD_LOKET_ID, $this->request->outletId);
        $message->set(AppMessage::FIELD_PIN, $this->request->pin);
        $message->set(AppMessage::FIELD_VIA, "WEB");

        $isDongle = FALSE;
        if (strlen($this->request->key) > 8) {
            if (self::isDongle($this, $this->request->outletId)) {
                $arrayDongle = self::getKeyDongle($this, $this->request->outletId, FmssRequestDecryptor::decrypt($this->request->key));
                $this->request->key = $arrayDongle["KEY"];
                if (empty($arrayDongle["KEY"])) {
                    $this->response->setStatus("01", "Key Anda tidak cocok / telah kadaluarsa");
                    exit();
                } else {
                    $message->set(AppMessage::FIELD_STEP, 2);
                    $message->set(AppMessage::FIELD_VIA, "MOBILE_SMART");
                    $message->set(AppMessage::FIELD_TOKEN, $arrayDongle["KEY"]);
                    $this->sendToCore($message);
                    $isDongle = TRUE;
                }
            } else {
                $this->response->setStatus("01", "Key Anda tidak cocok / telah kadaluarsa");
                exit();
            }
        }

        if (isset($this->request->key) && $this->request->key === 'DEMO') {
            $message->set(AppMessage::FIELD_TOKEN, 'DEMO');
            $this->sendToCore($message);
        } else if (isset($this->request->key) && ($this->request->key === 'travel' || $this->request->key === 'Travel')) // untuk cek login requestkey//
        {
            $message->set(AppMessage::FIELD_TOKEN, 'travel');
            $this->sendToCore($message);
        } else if (Utility::isPartner($this->request->outletId)) {
            $message->set(AppMessage::FIELD_TOKEN, 'travel'); //
            $this->sendToCore($message);
        } else {
            if ($this->getOutletToken($this->request->outletId, $this->request->key) === 'travel') {
                $message->set(AppMessage::FIELD_TOKEN, $this->request->key);
                $this->sendToCore($message);
            } else {
                if (!$isDongle) {
                    $message->set(AppMessage::FIELD_STATUS, "02");
                    $message->set(AppMessage::FIELD_KETERANGAN, "Key Anda tidak cocok / telah kadaluarsa");
                }
            }
        }
        SignInResponseParser::instance()->parse($message)->into($this);
    }

    public function is_partner($outletId)
    {
        return substr($outletId, 0, 2) == "HH";
    }

    public static function isDongle(APIController $apiController,  $outletId)
    {
        $key = "travel";
        $object = $apiController->db->fetchAll("SELECT id_outlet FROM fmss.mt_outlet WHERE id_outlet = ? and token = crypt( ? , token )", Db::FETCH_OBJ, [strtoupper(trim($outletId)), $key]);
        return (sizeof($object) > 0) ? FALSE : TRUE;
    }

    public static function getKeyDongle(APIController $apiController, $id_outlet, $key)
    {
        $checked = "";
        $today = date("Y-m-d");
        $date_expired = date("Y-m-d 00:00:00", strtotime($today . " + 1 DAY"));
        $isWithDongle = FALSE;
        if (strlen($key) === 6) {
        } else {
            $isWithDongle = TRUE;
        }
        if ($key === "travel") {
            if (date('Ymd') < "20121031" || $id_outlet == 'BS0003' || $id_outlet == 'BS0004' || $id_outlet == 'AN47103') { //ID AN47103 dimasukkan untuk testing ANS (request dari pak wahyu dengan implementator mirza)
                $checked = "travel";
            } else {
                $checked = "XYZ";
            }
        } else {
            if (sizeof($apiController->db->fetchAll("SELECT token FROM mt_outlet WHERE id_outlet = ? AND token = CRYPT('travel',token)", Db::FETCH_OBJ, [strtoupper(trim($id_outlet))])) == 0) {
                if (strlen($key) === 6) {
                    $checked = "XYZ";
                } else {
                    $checked = $key;
                }
            } else {
                $objDateCreated = $apiController->db->fetchAll("SELECT date_created FROM fmss.mobile_token WHERE id_outlet = ? AND date_expired >= ? ORDER BY date_created DESC LIMIT 2", Db::FETCH_OBJ, [strtoupper(trim($id_outlet)), $date_expired]);
                if (!(sizeof($objDateCreated) > 0)) {
                    $checked = "XYZ";
                } else {
                    $in_datecreated = "";
                    foreach ($objDateCreated as $vals) {
                        $in_datecreated .= $in_datecreated == "" ? "'" . $vals->date_created . "'" : ", '" . $vals->date_created . "'";
                    }
                    if (!(sizeof($apiController->db->fetchAll("SELECT date_created FROM fmss.mobile_token WHERE id_outlet = ? AND date_expired >= ? AND date_created IN (" . $in_datecreated . ") AND key = CRYPT(?, key) ", Db::FETCH_OBJ, [strtoupper(trim($id_outlet)), $date_expired, trim($this->_replace($key))])) > 0)) {
                        $checked = "XYZ";
                    } else {
                        $checked = "travel";
                    }
                }
            }
        }

        return array("KEY" => $checked, "ISDONGLE" => $isWithDongle);
    }
}