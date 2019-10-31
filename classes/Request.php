<?php

declare(strict_types=1);

namespace MessageCloud\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use MessageCloud\Gateway\Exceptions\SMSMessageException;
use Psr\Log\LoggerInterface;

abstract class Request
{
    public const GATEWAY_API_ENDPOINT = 'http://client.txtnation.com/gateway.php';

    public const DEFAULT_LOG_LOCATION = '../logs/messages.txt';
    public const DEFAULT_MAX_LOG_FILES = 7;

    /**
     * @var LoggerInterface
     */
    protected $objLogger;

    protected $strUsername = null;
    protected $strPassword = null;
    protected $strMsisdn = null;
    protected $strBody = null;
    protected $strSenderId = null;
    protected $strId = null;
    protected $strNetwork = null;
    protected $fltValue = null;
    protected $strCurrency = null;
    protected $intReply = null;
    protected $intCategory = null;
    protected $strEncoding = null;
    protected $blBinary = null;
    protected $strUdh = null;

    abstract protected function validate();

    public function setLogger(LoggerInterface $logger): void
    {
        $this->objLogger = $logger;
    }

    public function send()
    {
        if (!$this->validate()) {
            throw new SMSMessageException('Could not send message');
        }

        if (empty($this->strId)) {
            $this->strId = $this->generateRandomString();
        }

        $arrParams = [
            'cc' => $this->strUsername,
            'ekey' => $this->strPassword,
            'message' => $this->strBody,
            'title' => $this->strSenderId,
            'network' => $this->strNetwork,
            'value' => $this->fltValue,
            'currency' => $this->strCurrency,
            'encoding' => $this->strEncoding,
            'number' => $this->strMsisdn,
            'id' => $this->strId,
            'reply' => $this->intReply,
        ];

        if ($this->blBinary) {
            $arrParams['binary'] = (int) $this->blBinary;
            $arrParams['udh'] = $this->strUdh;
        }

        if ($this->intCategory) {
            $arrParams['smscat'] = $this->intCategory;
        }

        $this->objLogger->debug('Sending the following to MessageCloud:', $arrParams);

        $objClient = new Client([
            'base_uri' => 'http://client.txtnation.com/', 'timeout'  => 10.0,
        ]);

        $objResponse = $objClient->get('/gateway.php', [
            RequestOptions::QUERY => $arrParams,
            RequestOptions::SYNCHRONOUS => true,
            RequestOptions::ALLOW_REDIRECTS => true,
            RequestOptions::HEADERS => [
                'User-agent' => 'MessageCloudGatewayLibraryPHP/1.0',
            ],
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $objResult = new SMSMessageResult($objResponse);
        $objResult->setCallbackId($this->strId);

        if (!$objResult->success()) {
            $this->objLogger->alert('Message was not sent. ', ['error' => $objResult->getErrorMessage()]);
        }

        return $objResult;
    }

    private function generateRandomString($length = 36)
    {
        $characters = '0123456789abcdef';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 1; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
