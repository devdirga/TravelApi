<?php

namespace Travel\Wisata;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\WisataMessage;
use Travel\Libraries\Parser\Wisata\SearchResponseParser;

class SearchController extends APIController
{
    protected $invoking = "Search Wisata";

    public function indexAction()
    {
        $message = new WisataMessage($this);

        SearchResponseParser::instance()->parse($message)->into($this);
    }
}