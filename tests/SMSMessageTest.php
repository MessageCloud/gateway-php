<?php

require_once 'vendor/autoload.php';

use MessageCloud\Gateway\SMSMessage;

class SMSMessageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->objMessage = new SMSMessage('Marc','test');
    }

    public function tearDown()
    {
        unset($this->objMessage);
    }

    /**
     * @dataProvider providerForBadMsisdn
     * @expectedException MessageCloud\Gateway\Exceptions\SMSMessageException
     */
    public function testBadMsisdnThrowsException($input)
    {
        $this->objMessage->msisdn($input);
    }

    /**
     * @dataProvider providerForValidMsisdn
     */
    public function testValidMsisdnReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf('MessageCloud\Gateway\SMSMessage', $this->objMessage->msisdn($input));
    }

    public function providerForValidMsisdn()
    {
        return [
            ['447528748500'],
            ['44752874850'],
            ['353857025834']
        ];
    }

    public function providerForBadMsisdn()
    {
        return [
            [''],
            ['44752874850a'],
            ['07528748500'],
            ['444752874850000'],
            ['60999'],
            [[]],
            [new stdClass]
        ];
    }

    /**
     * @dataProvider providerForBadId
     * @expectedException MessageCloud\Gateway\Exceptions\SMSMessageException
     */
    public function testBadIdThrowsException($input)
    {
        $this->objMessage->id($input);
    }

    /**
     * @dataProvider providerForValidId
     */
    public function testValidIdReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf('MessageCloud\Gateway\SMSMessage', $this->objMessage->id($input));
    }

    public function providerForValidId()
    {
        return [
            ['1'],
            ['12345'],
            [md5('12345')],
            [sha1('12345')],
            ['de305d54-75b4-431b-adb2-eb6b9e546014']
        ];
    }

    public function providerForBadId()
    {
        return [
            [''],
            [[]],
            [new stdClass]
        ];
    }

    /**
     * @dataProvider providerForBadBody
     * @expectedException MessageCloud\Gateway\Exceptions\SMSMessageException
     */
    public function testBadBodyThrowsException($input)
    {
        $this->objMessage->body($input);
    }

    /**
     * @dataProvider providerForValidBody
     */
    public function testValidBodyReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf('MessageCloud\Gateway\SMSMessage', $this->objMessage->body($input));
    }

    public function providerForValidBody()
    {
        return [
            [''],
            ['Hello, world!'],
            ['This is a long message that will represent a concatenated message for the sake of this unit test. Concatenated messages are normally over 160 characters long and are automatically handled by MessageCloud.']
        ];
    }

    public function providerForBadBody()
    {
        return [
            [[]],
            [new stdClass]
        ];
    }

    /**
     * @dataProvider providerForBadSenderId
     * @expectedException MessageCloud\Gateway\Exceptions\SMSMessageException
     */
    public function testBadSenderIdThrowsException($input)
    {
        $this->objMessage->senderId($input);
    }

    /**
     * @dataProvider providerForValidSenderId
     */
    public function testValidSenderIdReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf('MessageCloud\Gateway\SMSMessage', $this->objMessage->senderId($input));
    }

    public function providerForValidSenderId()
    {
        return [
            ['MessageCloud'],
            ['447528748500'],
            ['07528748500']
        ];
    }

    public function providerForBadSenderId()
    {
        return [
            [''],
            ['thissenderidistoolong'],
            ['+447528748500'],
            ['00447528748500'],
            [[]],
            [new stdClass]
        ];
    }

    /**
     * @dataProvider providerForBadValue
     * @expectedException MessageCloud\Gateway\Exceptions\SMSMessageException
     */
    public function testBadValueThrowsException($input)
    {
        $this->objMessage->value($input);
    }

    /**
     * @dataProvider providerForValidValue
     */
    public function testValidValueReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf('MessageCloud\Gateway\SMSMessage', $this->objMessage->value($input));
    }

    public function providerForValidValue()
    {
        return [
            [0.00],
            ['0.00'],
            [0],
            [10],
            [12.5],
            [100]
        ];
    }

    public function providerForBadValue()
    {
        return [
            [-1],
            ['-10'],
            [[]],
            [new stdClass]
        ];
    }

    /**
     * @expectedException MessageCloud\Gateway\Exceptions\SMSMessageException
     */
    public function testNetworkTooLong()
    {
        $this->objMessage->network('myverylongnetworknamethatwontworkifwesendtotxtnation');
    }

    public function testNetworkReturnsSMSMessageObject()
    {
        $this->assertInstanceOf('MessageCloud\Gateway\SMSMessage', $this->objMessage->network('international'));
    }

    /**
     * @expectedException MessageCloud\Gateway\Exceptions\SMSMessageException
     */
    public function testInternationalMessageHasValue()
    {
        $this->objMessage->network('international')->value(1.00)->send();
    }
}
