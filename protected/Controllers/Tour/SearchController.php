<?php

namespace Travel\Tour;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TourMessage;
use Travel\Libraries\Parser\Tour\SearchResponseParser;

class SearchController extends APIController
{
    protected $invoking = "Search Tour";

    public function indexAction()
    {
        $message = new TourMessage($this);

        $this->sendToCore($message);

        SearchResponseParser::instance()->parse($message)->into($this);
    }
}