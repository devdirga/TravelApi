<?php

namespace Travel\Tour;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\TourMessage;
use Travel\Libraries\Parser\Tour\BookResponseParser;

class BookController extends APIController
{
    protected $invoking = "Book Tour";

    public function indexAction()
    {
        $message = new TourMessage($this);

        $this->sendToCore($message);

        BookResponseParser::instance()->parse($message)->into($this);
    }
}