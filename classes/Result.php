<?php

declare(strict_types=1);

namespace MessageCloud\Gateway;

use Teapot\StatusCode;

abstract class Result
{
    protected $strSuccess = 'OK';

    public function success()
    {
        return ((StatusCode::OK === $this->objResult->getStatusCode())
            && ($this->strSuccess === (string) $this->objResult->getBody()));
    }

    abstract public function getErrorCode();

    abstract public function getErrorMessage();
}
