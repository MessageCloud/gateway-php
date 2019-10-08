<?php

namespace MessageCloud\Gateway;

use Psr\Http\Message\ResponseInterface;

class SMSMessageResult extends Result
{
    // the response to expect when everything went well
    protected $strSuccess = 'SUCCESS';

    protected $strId;

    // a few error codes
    const ERROR_NO_CREDITS = 'NO CREDITS';
    const ERROR_BARRED = 'BARRED';

    const ERROR_IR_101 = 'IR-101';
    const ERROR_IR_102 = 'IR-102';
    const ERROR_IR_103 = 'IR-103';
    const ERROR_IR_104 = 'IR-104';

    const ERROR_IR_401 = 'IR-401';
    const ERROR_IR_403 = 'IR-403';
    const ERROR_IR_404 = 'IR-404';
    const ERROR_IR_405 = 'IR-405';
    const ERROR_IR_409 = 'IR-409';
    const ERROR_IR_410 = 'IR-410';
    const ERROR_IR_412 = 'IR-412';
    const ERROR_IR_413 = 'IR-413';
    const ERROR_IR_414 = 'IR-414';
    const ERROR_IR_415 = 'IR-415';
    const ERROR_IR_416 = 'IR-416';
    const ERROR_IR_417 = 'IR-417';
    const ERROR_IR_418 = 'IR-418';
    const ERROR_IR_419 = 'IR-419';
    const ERROR_IR_420 = 'IR-420';

    const ERROR_E_100 = 'E-100';
    const ERROR_E_101 = 'E-101';
    const ERROR_E_102 = 'E-102';
    const ERROR_E_103 = 'E-103';
    const ERROR_E_105 = 'E-105';
    const ERROR_E_107 = 'E-107';
    const ERROR_E_108 = 'E-108';
    const ERROR_E_109 = 'E-109';

    const ERROR_UNKNOWN = 'Unrecognised error code returned. Please contact MessageCloud Support at help@messagecloud.com for more assistance.';


    // how the error codes translate into real person speak
    protected $arrErrorMessages = [
        self::ERROR_NO_CREDITS => 'No credits remaining. Contact help@messagecloud.com and request more credits.',
        self::ERROR_BARRED => 'The end user has previously sent in a STOP request preventing any further messages.',

        self::ERROR_IR_101 => 'Duplicate post. You have already replied to a message this ID. In most cases you can only reply to a message once.',
        self::ERROR_IR_102 => 'Missing details. A binary transaction has been requested, but the UDH has not been specified. You should set this with the SMSMessage::udh() method.',
        self::ERROR_IR_103 => 'Invalid username or password. If you are new to MessageCloud, you should create an account at https://my.messagecloud.com/register. The username and password values are case sensitive.',
        self::ERROR_IR_104 => 'Invalid details. Unable to find inbound record. Please check the provided SmsMessage::id() and SMSMessage::network() match the values we posted to you during the initial request.',

        self::ERROR_IR_401 => 'The SMSMessage::reply() value is not being correctly evaluated. Please ensure you are sending it correctly.',
        self::ERROR_IR_403 => 'The SMSMessage::id() value must be numeric for reply messages. A non-numeric value has been set.',
        self::ERROR_IR_404 => 'The SMSMessage::msisdn() value was not set correctly. We need to know the phone number to which you are sending your SMS.',
        self::ERROR_IR_405 => 'The SMSMessage::msisdn() value was not numeric. Please note that all phone number need to be numeric in value.',
        self::ERROR_IR_410 => 'The SMSMessage::value() value was not numeric. Please ensure that the variable is numeric, e.g. 1.00.',
        self::ERROR_IR_412 => 'The SMSMessage::network() / SMSMessage::value() combination is not available.',
        self::ERROR_IR_413 => 'SMSMessage::value() must be 0.00 or greater.',
        self::ERROR_IR_414 => 'Invalid username.',
        self::ERROR_IR_415 => 'Username not found.',
        self::ERROR_IR_416 => 'The SMSMessage::message() value was empty and must be used for this transaction.',
        self::ERROR_IR_417 => 'Your SMSMessage::message() value was too long for this transaction. Please ensure that the message does not exceed 160 characters.',
        self::ERROR_IR_418 => 'You cannot send a billed message to a MO-type SMSMessage::network(). MO-type networks are billed on the inbound message, not on the outbound message.',
        self::ERROR_IR_419 => 'You cannot send a billed message via a bulk/free SMSMessage::network().',
        self::ERROR_IR_420 => 'The SMSMessage::network() value was invalid. The network to which you are attempting to send was not recognised.',

        self::ERROR_E_100 => 'Please contact MessageCloud Support at help@messagecloud.com for more assistance.',
        self::ERROR_E_101 => 'Operator Error.',
        self::ERROR_E_102 => 'Tariff Error. Please check the credit value that you\'ve set.',
        self::ERROR_E_103 => 'Invalid Data. Please check your values and variables.',
        self::ERROR_E_105 => 'Invalid Operator ID.',
        self::ERROR_E_107 => 'Invalid Test. Please review your settings.',
        self::ERROR_E_108 => 'Sending failed as you have no credits remaining on your account.',
        self::ERROR_E_109 => 'Sending through your account via MessageCloud is currently disabled in this country.'
    ];

    protected $objResult;

    protected $strErrorCode = self::ERROR_UNKNOWN;

    public function __construct(ResponseInterface $objResult)
    {
        $this->objResult = $objResult;
    }

    public function setCallbackId($strId)
    {
        return ($this->strId = $strId);
    }

    public function getCallbackId()
    {
        return $this->strId;
    }

    public function getErrorCode()
    {
        $arrMatches = [];

        if (preg_match('/((IR|E)\-\d{3})|(NO CREDITS)|(BARRED)/', (string) $this->objResult->getBody(), $arrMatches)) {
            $this->strErrorCode = $arrMatches[0];
        }

        return $this->strErrorCode;
    }

    public function getErrorMessage()
    {
        return (!empty($this->arrErrorMessages[$this->strErrorCode])) ? $this->arrErrorMessages[$this->strErrorCode] : self::ERROR_UNKNOWN;
    }
}
