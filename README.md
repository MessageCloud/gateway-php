# txtNation Gateway PHP Library

The txtNation Gateway PHP Library works with PHP 5.3+. It is also compatible with PHP 7.

## Documentation

If you'd rather build on top of the Gateway API from scratch, further documentation can be found on the [txtNation Wiki](http://wiki.txtnation.com/).

## Prerequisites

This library has been developed and tested on Mac OS 10.11.4 and Fedora 22.

The following README assumes that you are using the following PHP extensions:

- php-curl
- php-mbstring

## Installation

### Building with Composer

Using [Composer](https://getcomposer.org/) you can easily download and build the app:

```bash
$ composer require txtnation/txtnation-gateway-php
```

If you are using this code in production you should use the `--no-dev` flag to prevent development-only packages being downloaded and installed.

### Importing the Library

All you need to do to get started is add the following line at the top of your script:

```php
require_once 'vendor/autoload.php';
```

To test that the library is working correctly you can run the following:

```php
$objMessage = new SMSMessage(YOUR_COMPANY_NAME_HERE, YOUR_EKEY_HERE);
$objResult = $objMessage->msisdn('447528748500')->body('Hello, world!')->senderId('txtNation')->send();

if ($objResult->success()) {
    echo $objResult->getCallbackId();
} else {
    echo 'Error sending message! Code: ' . $objResult->getErrorCode() . ' (' . $objResult->getErrorMessage() . ')';
}
```

To which you will get a result similar to the following:

```bash
$ php test.php
123e4567-e89b-12d3-a456-426655440000
```

The callback ID can be used when receiving the delivery reports for your requests. Each delivery report will contain an `id` parameter containing the ID returned by the SMSMessage::getCallbackId() function.

You can also include the following line underneath your `require_once()` function as a shortcut to the SMSMessage object:

```php
use txtNation\Gateway\SMSMessage as SMSMessage;
```

## Using the Library

Check out the [examples](examples/) of how to use this library. They can be found in the examples/ directory.

## Testing

You can test the library by using the following command (dev only):

```bash
$ composer test
```
