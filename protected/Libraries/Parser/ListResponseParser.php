<?php

namespace Travel\Libraries\Parser;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\BaseMessage;

interface ListResponseParser
{
    function parse(BaseMessage $message, APIController $apiController);
    function into(APIController $apiController);
}