<?php

namespace MessageCloud\Gateway;

use Teapot\StatusCode;

abstract class Result
{
    protected $strSuccess = 'OK';

    public function success()
    {
        return ((StatusCode::OK === $this->objResult->getStatusCode()) && ($this->strSuccess === (string) $this->objResult->getBody()));
    }

    abstract function getErrorCode();

    abstract function getErrorMessage();
}
