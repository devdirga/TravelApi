<?php

namespace Travel\Libraries\Parser;

use Travel\Libraries\Message\BaseMessage;

class BaseResponseParser
{
    /*
     * Parse message.
     * 
     * @return ResponseParser
     */
    public function parse(BaseMessage $message)
    {
        $this->message = $message;

        return $this;
    }

    /*
     * Return instance parser.
     * 
     * @return ResponseParser
     */
    public static function instance()
    {
        return new static();
    }
}