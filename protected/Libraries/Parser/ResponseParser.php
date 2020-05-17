<?php

namespace Travel\Libraries\Parser;

use Travel\Libraries\APIController;
use Travel\Libraries\Message\BaseMessage;

interface ResponseParser
{
    function parse(BaseMessage $message);
    function into(APIController $apiController);
}
