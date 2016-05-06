<?php

namespace txtNation\Gateway;

use Monolog\Logger;
use txtNation\Gateway\Exceptions\SMSMessageException;
use Respect\Validation\Validator;

class SMSMessage extends Request
{
    const LOGGING = 'logging';

    const DEFAULT_FREE_NETWORK = 'INTERNATIONAL';
    const DEFAULT_CURRENCY = 'GBP';
    const DEFAULT_VALUE = 0.00;

    protected $strUsername = null;
    protected $strPassword = null;
    protected $strMsisdn = null;
    protected $strBody = null;
    protected $strSenderId = null;
    protected $strId = null;
    protected $strNetwork = null;
    protected $fltValue = null;
    protected $strCurrency = null;
    protected $blIsReply = null;
    protected $strEncoding = null;
    protected $blBinary = null;
    protected $strUdh = null;

    protected $arrOptions = [
        self::LOGGING => true
    ];

    public function __construct($strUsername, $strPassword, $arrOptions = [])
    {
        $this->strUsername = $strUsername;
        $this->strPassword = $strPassword;

        $this->arrOptions = array_merge($this->arrOptions, $arrOptions);

        $this->objLogger = new Logger(__CLASS__);

        if ($this->arrOptions[self::LOGGING]) $this->startLogging();

        $this->objLogger->addDebug('Message object constructed');
    }

    public function msisdn($strMsisdn)
    {
        if (!(Validator::numeric()->notEmpty()->length(10, 12)->not(Validator::startsWith('0'))->validate($strMsisdn))) {
            $this->objLogger->addError('MSISDN must be a numeric string between 10 and 12 characters long in international format');

            throw new SMSMessageException('MSISDN must be a numeric string between 10 and 12 characters long in international format');
        }

        $this->strMsisdn = $strMsisdn;

        $this->objLogger->addDebug('MSISDN has been set to ' . $strMsisdn);

        return $this;
    }

    public function id($strId)
    {
        if (!(Validator::stringType()->notEmpty()->validate($strId))) {
            $this->objLogger->addError('ID must be a string');

            throw new SMSMessageException('ID must be a string');
        }

        $this->strId = $strId;

        $this->objLogger->addDebug('ID has been set to ' . $strId);

        return $this;
    }

    public function body($strBody)
    {
        if (!(Validator::stringType()->validate($strBody))) {
            $this->objLogger->addError('Message body must be a string');

            throw new SMSMessageException('Message body must be a string');
        }

        $this->strBody = $strBody;

        $this->objLogger->addDebug('Message body has been set to ' . $strBody);

        return $this;
    }

    public function senderId($strSenderId)
    {
        if (!(Validator::stringType()->notEmpty()->length(1,12)->validate($strSenderId))) {
            $this->objLogger->addError('SenderId must be a string between 1 and 12 characters long');

            throw new SMSMessageException('SenderId must be a string between 1 and 12 characters long');
        }

        $this->strSenderId = $strSenderId;

        $this->objLogger->addDebug('Sender ID has been set to ' . $strSenderId);

        return $this;
    }

    public function network($strNetwork)
    {
        if (!(Validator::stringType()->notEmpty()->length(1,50)->validate($strNetwork))) {
            $this->objLogger->addError('Network must be a string');

            throw new SMSMessageException('Network must be a string');
        }

        $this->strNetwork = $strNetwork;

        $this->objLogger->addDebug('Network has been set to ' . $strNetwork);

        return $this;
    }

    public function value($fltValue)
    {
        if (!(Validator::floatVal()->min(0, true)->validate($fltValue))) {
            $this->objLogger->addError('Value must be a floating point number');

            throw new SMSMessageException('Value must be a floating point number');
        }

        $this->fltValue = (float) $fltValue;

        $this->objLogger->addDebug('Value has been set to ' . $fltValue);

        return $this;
    }

    public function currency($strCurrency)
    {
        if (!(Validator::stringType()->notEmpty()->length(3, 3)->validate($strCurrency))) {
            $this->objLogger->addError('Currency should be in ISO 4217 standard, e.g. USD, EUR, GBP');

            throw new SMSMessageException('Currency should be in ISO 4217 standard, e.g. USD, EUR, GBP');
        }

        $this->strCurrency = $strCurrency;

        $this->objLogger->addDebug('Currency has been set to ' . $strCurrency);

        return $this;
    }

    public function reply($blReply)
    {
        if (!(Validator::boolean()->validate($blReply))) {
            $this->objLogger->addError('Reply must be TRUE or FALSE');

            throw new SMSMessageException('Reply must be TRUE or FALSE');
        }

        $this->blReply = (bool) $blReply;

        $this->objLogger->addDebug('Reply has been set to ' . $blReply);

        return $this;
    }

    public function udh($strUdh)
    {
        if (!(Validator::stringType()->notEmpty()->length(1,255)->validate($strUdh))) {
            $this->objLogger->addError('UDH must be a string');

            throw new SMSMessageException('UDH must be a string');
        }

        $this->strUdh = $strUdh;

        $this->objLogger->addDebug('UDH has been set to ' . $strNetwork);

        return $this;
    }

    public function binary($blBinary)
    {
        if (!(Validator::boolean()->validate($blBinary))) {
            $this->objLogger->addError('Binary must be TRUE or FALSE');

            throw new SMSMessageException('Binary must be TRUE or FALSE');
        }

        $this->blBinary = (bool) $blBinary;

        $this->objLogger->addDebug('Binary has been set to ' . $blBinary);

        return $this;
    }

    protected function validate()
    {
        $this->objLogger->addDebug('Validating the request');

        if (!$this->strMsisdn) {
            $this->objLogger->addError('MSISDN must be set');

            throw new SMSMessageException('MSISDN must be set');
        }

        if (!$this->strSenderId) {
            $this->objLogger->addError('Sender ID must be set');

            throw new SMSMessageException('Sender ID must be set');
        }

        if (!$this->strBody) {
            $this->objLogger->addWarning('No message was set on the outgoing message');
        }

        if (0.0 === (float)$this->fltValue && is_null($this->strNetwork)) {
            $this->objLogger->addDebug('Automatically setting the network to INTERNATIONAL for a 0 value message');

            $this->strNetwork = self::DEFAULT_FREE_NETWORK;
        }

        if (is_null($this->strCurrency)) {
            $this->objLogger->addDebug('Automatically setting the currency to ' . self::DEFAULT_CURRENCY);

            $this->strCurrency = self::DEFAULT_CURRENCY;
        }

        if (is_null($this->fltValue)) {
            $this->objLogger->addDebug('Automatically setting the message value to ' . self::DEFAULT_VALUE);

            $this->fltValue = self::DEFAULT_VALUE;
        }

        if ((strtoupper(self::DEFAULT_FREE_NETWORK) === strtoupper($this->strNetwork)) && (0.00 < $this->fltValue)) {
            $this->objLogger->addError('Free messages cannot have a value');

            throw new SMSMessageException('Free messages cannot have a value');
        }

        return true;
    }
}
