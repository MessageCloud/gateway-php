<?php

declare(strict_types=1);

namespace MessageCloudTest\Gateway;

use MessageCloud\Gateway\Exceptions\SMSMessageException;
use MessageCloud\Gateway\SMSMessage;
use PHPUnit\Framework\TestCase;
use stdClass;

class SMSMessageTest extends TestCase
{
    private $objMessage;

    public function setUp(): void
    {
        $this->objMessage = new SMSMessage('Marc', 'test');
    }

    public function tearDown(): void
    {
        unset($this->objMessage);
    }

    /**
     * @dataProvider providerForBadMsisdn
     */
    public function testBadMsisdnThrowsException($input)
    {
        $this->expectException(SMSMessageException::class);
        $this->objMessage->msisdn($input);
    }

    /**
     * @dataProvider providerForValidMsisdn
     */
    public function testValidMsisdnReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf(SMSMessage::class, $this->objMessage->msisdn($input));
    }

    public function providerForValidMsisdn()
    {
        return [
            ['447528748500'],
            ['44752874850'],
            ['353857025834'],
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
            [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerForBadId
     */
    public function testBadIdThrowsException($input)
    {
        $this->expectException(SMSMessageException::class);
        $this->objMessage->id($input);
    }

    /**
     * @dataProvider providerForValidId
     */
    public function testValidIdReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf(SMSMessage::class, $this->objMessage->id($input));
    }

    public function providerForValidId()
    {
        return [
            ['1'],
            ['12345'],
            [md5('12345')],
            [sha1('12345')],
            ['de305d54-75b4-431b-adb2-eb6b9e546014'],
        ];
    }

    public function providerForBadId()
    {
        return [
            [''],
            [[]],
            [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerForBadBody
     */
    public function testBadBodyThrowsException($input)
    {
        $this->expectException(SMSMessageException::class);
        $this->objMessage->body($input);
    }

    /**
     * @dataProvider providerForValidBody
     */
    public function testValidBodyReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf(SMSMessage::class, $this->objMessage->body($input));
    }

    public function providerForValidBody()
    {
        return [
            [''],
            ['Hello, world!'],
            ['This is a long message that will represent a concatenated message for the sake of this unit test. 
            Concatenated messages are normally over 160 characters long and are automatically handled by MessageCloud.'],
        ];
    }

    public function providerForBadBody()
    {
        return [
            [[]],
            [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerForBadSenderId
     */
    public function testBadSenderIdThrowsException($input)
    {
        $this->expectException(SMSMessageException::class);
        $this->objMessage->senderId($input);
    }

    /**
     * @dataProvider providerForValidSenderId
     */
    public function testValidSenderIdReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf(SMSMessage::class, $this->objMessage->senderId($input));
    }

    public function providerForValidSenderId()
    {
        return [
            ['MessageCloud'],
            ['447528748500'],
            ['07528748500'],
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
            [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerForBadValue
     */
    public function testBadValueThrowsException($input)
    {
        $this->expectException(SMSMessageException::class);
        $this->objMessage->value($input);
    }

    /**
     * @dataProvider providerForValidValue
     */
    public function testValidValueReturnsSMSMessageObject($input)
    {
        $this->assertInstanceOf(SMSMessage::class, $this->objMessage->value($input));
    }

    public function providerForValidValue()
    {
        return [
            [0.00],
            ['0.00'],
            [0],
            [10],
            [12.5],
            [100],
        ];
    }

    public function providerForBadValue()
    {
        return [
            [-1],
            ['-10'],
            [[]],
            [new stdClass()],
        ];
    }

    public function testNetworkTooLong()
    {
        $this->expectException(SMSMessageException::class);
        $this->objMessage->network('myverylongnetworknamethatwontworkifwesendtotxtnation');
    }

    public function testNetworkReturnsSMSMessageObject()
    {
        $this->assertInstanceOf(SMSMessage::class, $this->objMessage->network('international'));
    }

    public function testInternationalMessageHasValue()
    {
        $this->expectException(SMSMessageException::class);
        $this->objMessage->network('international')->value(1.00)->send();
    }
}
