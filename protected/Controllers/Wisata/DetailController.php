<?php

namespace Travel\Wisata;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\WisataMessage;
use Travel\Libraries\Parser\Wisata\DetailResponseParser;

class DetailController extends APIController
{
    protected $invoking = "Search Wisata";

    public function indexAction()
    {
        $message = new WisataMessage($this);

        DetailResponseParser::instance()->parse($message)->into($this);
    }
}