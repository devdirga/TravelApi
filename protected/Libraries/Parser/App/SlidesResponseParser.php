<?php

namespace Travel\Libraries\Parser\App;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\APIController;
use Travel\Libraries\Message\AppMessage;
use Travel\Libraries\Models\Slidesbf;
use Travel\Libraries\Models\Slide;

class SlidesResponseParser extends BaseResponseParser implements ResponseParser
{

    /**
     * AppMessage.
     * 
     * @var AppMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {

        if (isset($apiController->request->isFromFp)) {
            $slides = Slidesbf::find();
        } else {
            $slides = Slide::find(array(
                'conditions' => 'isActive = ?1 AND app = ?2',
                'bind' => array(
                    1 => 1,
                    2 => "B2B-MOBILE"
                )
            ));
        }

        if (isset($apiController->request->isFromFp)) {
            if (count($slides) > 0) {
                foreach ($slides as $slide) {
                    $object = new \stdClass();
                    $object->product = "All";
                    $object->path = $slide->urlImage;
                    $object->url = $slide->targetLink;

                    $apiController->response->data[] = $object;
                }
            } else {
                $apiController->response->setStatus("01", "Slide empty.");
            }
        } else {

            if (count($slides) > 0) {
                foreach ($slides as $slide) {
                    $object = new \stdClass();
                    $object->product = $slide->produk;
                    $object->path = $apiController->config->app->cdn . $slide->path;
                    $object->url = $slide->url;

                    $apiController->response->data[] = $object;
                }
            } else {
                $apiController->response->setStatus("01", "Slide empty.");
            }
        }
    }
}