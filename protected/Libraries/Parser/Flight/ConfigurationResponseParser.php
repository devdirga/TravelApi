<?php

namespace Travel\Libraries\Parser\Flight;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\FlightMessage;
use Travel\Libraries\APIController;
use Phalcon\Db;

class ConfigurationResponseParser extends BaseResponseParser implements ResponseParser
{

    /**
     * Flight Message.
     * 
     * @var FlightMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $rc = $this->message->get(FlightMessage::FIELD_STATUS);
        $rd = $this->message->get(FlightMessage::FIELD_KETERANGAN);

        if ($rc == "00") {
            $settings = explode("|", $this->message->get(FlightMessage::FIELD_CLASSNAME1));
            $configurations = explode("|", $this->message->get(FlightMessage::FIELD_CLASSNAME4));
            //$airports = explode(";", $this->message->get(FlightMessage::FIELD_MESSAGE));

            $apiController->response->setDataAsObject();

            $apiController->response->data->settings = array();
            $apiController->response->data->configurations = array();
            $apiController->response->data->airports = array();

            //SET DEFAULT MASKAPAI
            //            array_push($settings, "TPID=0=0=BATIK AIR=1=1=0=http://static.scash.bz/Travel/asset/maskapai/TPID.png=1=http://www.Travel.co.id/lion.php");
            //            array_push($settings, "TPIW=0=0=WINGS AIR=1=1=0=http://static.scash.bz/Travel/asset/maskapai/TPIW.png=1=http://www.Travel.co.id/lion.php");


            foreach ($settings as $setting) {
                $exploded = explode("=", $setting);

                $object = new \stdClass();

                $object->airline = $exploded[0];
                $object->isActive = $exploded[1];
                $object->customAdmin = $exploded[2];
                $object->airlineName = str_replace(" INDONESIA", "", $exploded[3]);
                $object->isCaptcha = $exploded[4];
                $object->isInfant = $exploded[5];
                $object->isChild = $exploded[6];
                $object->icon = $exploded[7];
                $object->switcherId = $exploded[8];
                $object->newsUrl = $exploded[9];

                $apiController->response->data->settings[] = $object;
            }

            foreach ($configurations as $configuration) {
                $exploded = explode("=", $configuration);

                $object = new \stdClass();

                $object->airline = $exploded[0];
                $object->isActive = $exploded[1];
                $object->isFirstName = $exploded[2];
                $object->isLastName = $exploded[3];
                $object->isTitle = $exploded[4];
                $object->isPhone = $exploded[5];
                $object->isMobilePhone = $exploded[6];
                $object->isBirthDay = $exploded[7];
                $object->isIdentityNumber = $exploded[8];
                $object->isNationality = $exploded[9];
                $object->isAddress = $exploded[10];
                $object->isEmail = $exploded[11];
                $object->isPostalCode = $exploded[12];

                $apiController->response->data->configurations[] = $object;
            }

            /*
            foreach ($airports as $airport)
            {
                $exploded = explode(",", $airport);
                $explodedCodeName = explode("/", $exploded[0]);
                
                if($explodedCodeName[0] != "ZZZ" && $explodedCodeName[0] != "")
                {
                    $object = new \stdClass();
                
                    $object->code = trim($explodedCodeName[0]);
                    $object->name = isset($explodedCodeName[1]) ? trim($explodedCodeName[1]) : "Airport";
                    $object->group = isset($exploded[1]) ? trim($exploded[1]) : "Indonesia";
                    $object->is_international = $this->is_international($object->code, $apiController);

                    $apiController->response->data->airports[] = $object;
                }
            }
            $apiController->response->data->nationality = $this->nationality();
             */
        }

        $apiController->response->setStatus($rc, $rc == "00" ? "Success" : $rd);
    }

    public function is_international($city_code, $apiController)
    {

        $q_destination = $apiController->db->query("select is_international from mt_destination where city_code= ?", [$city_code]);
        $q_destination->setFetchMode(Db::FETCH_OBJ);
        $data = $q_destination->fetch();

        return $data->is_international;
    }
}