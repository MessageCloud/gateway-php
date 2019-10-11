<?php

declare(strict_types=1);

namespace MessageCloud\Gateway;

abstract class Result
{
    protected $objResult;
    protected $strSuccess = 'OK';

    public function success()
    {
        return (200 === $this->objResult->getStatusCode()
            && ($this->strSuccess === (string) $this->objResult->getBody()));
    }

    abstract public function getErrorCode();

    abstract public function getErrorMessage();
}
