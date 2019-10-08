<?php

require_once __DIR__.'/../vendor/autoload.php';

use MessageCloud\Gateway\SMSMessage;

date_default_timezone_set('Europe/London');

$objMessage = new SMSMessage(YOUR_CLIENT_NAME, YOUR_EKEY);

$objResult = $objMessage->msisdn('447528748500')->body('Hello, world!')->senderId('MessageCloud')->send();

if ($objResult->success()){
    echo 'Message sent!';
} else {
    echo 'Error sending message! Code: ' . $objResult->getErrorCode() . ' (' . $objResult->getErrorMessage() . ')';
}
