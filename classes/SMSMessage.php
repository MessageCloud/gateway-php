<?php

declare(strict_types=1);

namespace MessageCloud\Gateway;

use MessageCloud\Gateway\Exceptions\SMSMessageException;
use Psr\Log\NullLogger;
use Respect\Validation\Validator;

class SMSMessage extends Request
{
    public const LOGGING = 'logging';
    public const LOGGER  = 'logger';

    public const DEFAULT_FREE_NETWORK = 'INTERNATIONAL';
    public const DEFAULT_CURRENCY = 'GBP';
    public const DEFAULT_VALUE = 0.00;
    public const DEFAULT_REPLY = 0;

    protected $arrOptions = [
        self::LOGGING => true,
        self::LOGGER  => null,
    ];

    public function __construct($strUsername, $strPassword, $arrOptions = [])
    {
        $this->strUsername = $strUsername;
        $this->strPassword = $strPassword;

        $this->arrOptions = array_merge($this->arrOptions, $arrOptions);

        if ($this->arrOptions[self::LOGGER] === null) {
            $this->arrOptions[self::LOGGER] = new NullLogger();
        }

        $this->setLogger($this->arrOptions[self::LOGGER]);
        $this->objLogger->debug('Message object constructed');
    }

    public function msisdn($strMsisdn): self
    {
        if (!(Validator::numericVal()->notEmpty()->length(10, 12)->not(Validator::startsWith('0'))->validate($strMsisdn))) {
            $errorText = 'MSISDN must be a numeric string between 10 and 12 characters long in international format';
            $this->objLogger->error($errorText);
            throw new SMSMessageException($errorText);
        }

        $this->strMsisdn = $strMsisdn;

        $this->objLogger->debug('MSISDN has been set to ' . $strMsisdn);

        return $this;
    }

    public function id($strId): self
    {
        if (!(Validator::stringType()->notEmpty()->validate($strId))) {
            $this->objLogger->error('ID must be a string');

            throw new SMSMessageException('ID must be a string');
        }

        $this->strId = $strId;

        $this->objLogger->debug('ID has been set to ' . $strId);

        return $this;
    }

    public function body($strBody): self
    {
        if (!(Validator::stringType()->validate($strBody))) {
            $this->objLogger->error('Message body must be a string');

            throw new SMSMessageException('Message body must be a string');
        }

        $this->strBody = $strBody;

        $this->objLogger->debug('Message body has been set to ' . $strBody);

        return $this;
    }

    public function senderId($strSenderId): self
    {
        if (!(Validator::stringType()->notEmpty()->length(1, 12)->validate($strSenderId))) {
            $this->objLogger->error('SenderId must be a string between 1 and 12 characters long');

            throw new SMSMessageException('SenderId must be a string between 1 and 12 characters long');
        }

        $this->strSenderId = $strSenderId;

        $this->objLogger->debug('Sender ID has been set to ' . $strSenderId);

        return $this;
    }

    public function network($strNetwork): self
    {
        if (!(Validator::stringType()->notEmpty()->length(1, 50)->validate($strNetwork))) {
            $this->objLogger->error('Network must be a string');

            throw new SMSMessageException('Network must be a string');
        }

        $this->strNetwork = $strNetwork;

        $this->objLogger->debug('Network has been set to ' . $strNetwork);

        return $this;
    }

    public function value($fltValue): self
    {
        if (!(Validator::floatVal()->min(0, true)->validate($fltValue))) {
            $this->objLogger->error('Value must be a floating point number');

            throw new SMSMessageException('Value must be a floating point number');
        }

        $this->fltValue = (float) $fltValue;

        $this->objLogger->debug('Value has been set to ' . $fltValue);

        return $this;
    }

    public function currency($strCurrency): self
    {
        if (!(Validator::stringType()->notEmpty()->length(3, 3)->validate($strCurrency))) {
            $this->objLogger->error('Currency should be in ISO 4217 standard, e.g. USD, EUR, GBP');

            throw new SMSMessageException('Currency should be in ISO 4217 standard, e.g. USD, EUR, GBP');
        }

        $this->strCurrency = $strCurrency;

        $this->objLogger->debug('Currency has been set to ' . $strCurrency);

        return $this;
    }

    public function reply($intReply): self
    {
        if (!(Validator::intType()->between(0, 1)->validate($intReply))) {
            $this->objLogger->error('Reply must be 1 or 0');

            throw new SMSMessageException('Reply must be 1 or 0');
        }

        $this->intReply = (int) $intReply;

        $this->objLogger->debug('Reply has been set to ' . $intReply);

        return $this;
    }

    public function udh($strUdh): self
    {
        if (!(Validator::stringType()->notEmpty()->length(1, 255)->validate($strUdh))) {
            $this->objLogger->error('UDH must be a string');

            throw new SMSMessageException('UDH must be a string');
        }

        $this->strUdh = $strUdh;

        $this->objLogger->debug('UDH has been set to ' . $strUdh);


        return $this;
    }

    public function binary($blBinary): self
    {
        if (!(Validator::boolType()->validate($blBinary))) {
            $this->objLogger->error('Binary must be TRUE or FALSE');

            throw new SMSMessageException('Binary must be TRUE or FALSE');
        }

        $this->blBinary = (bool) $blBinary;

        $this->objLogger->debug('Binary has been set to ' . $blBinary);

        return $this;
    }

    public function category($intCategory): self
    {
        if (!(Validator::numeric()->notEmpty()->length(3, 3)->validate($intCategory))) {
            $this->objLogger->error('Category must be a numeric string with a length of 3.');

            throw new SMSMessageException('Category must be a numeric string with a length of 3.');
        }

        $this->intCategory = $intCategory;

        $this->objLogger->debug('Category has been set to ' . $intCategory);

        return $this;
    }

    protected function validate(): bool
    {
        $this->objLogger->debug('Validating the request');

        if (!$this->strMsisdn) {
            $this->objLogger->error('MSISDN must be set');

            throw new SMSMessageException('MSISDN must be set');
        }

        if (!$this->strSenderId) {
            $this->objLogger->error('Sender ID must be set');

            throw new SMSMessageException('Sender ID must be set');
        }

        if (!$this->strBody) {
            $this->objLogger->warning('No message was set on the outgoing message');
        }

        if (0.0 === (float) $this->fltValue && is_null($this->strNetwork)) {
            $this->objLogger->debug('Automatically setting the network to INTERNATIONAL for a 0 value message');

            $this->strNetwork = self::DEFAULT_FREE_NETWORK;
        }

        if (is_null($this->strCurrency)) {
            $this->objLogger->debug('Automatically setting the currency to ' . self::DEFAULT_CURRENCY);

            $this->strCurrency = self::DEFAULT_CURRENCY;
        }

        if (is_null($this->fltValue)) {
            $this->objLogger->debug('Automatically setting the message value to ' . self::DEFAULT_VALUE);

            $this->fltValue = self::DEFAULT_VALUE;
        }

        if (is_null($this->intReply)) {
            $this->objLogger->debug('Automatically setting the reply value to ' . self::DEFAULT_REPLY);

            $this->intReply = self::DEFAULT_REPLY;
        }

        if ((strtoupper(self::DEFAULT_FREE_NETWORK) === strtoupper($this->strNetwork)) && (0.00 < $this->fltValue)) {
            $this->objLogger->error('Free messages cannot have a value');

            throw new SMSMessageException('Free messages cannot have a value');
        }

        return true;
    }
}
