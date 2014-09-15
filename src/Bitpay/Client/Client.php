<?php
/**
 * The MIT License (MIT)
 * 
 * Copyright (c) 2014 BitPay, Inc.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Bitpay\Client;

use Bitpay\Config\Configuration;
use Bitpay\Client\Adapter\AdapterInterface;
use Bitpay\Client\RequestInterface;
use Bitpay\Client\Request;
use Bitpay\Client\ResponseInterface;
use Bitpay\Client\Response;
use Bitpay\InvoiceInterface;
use Bitpay\Network\NetworkInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Psr\Log\LoggerInterface;
use Bitpay\Util\Util;

/**
 * Client used to send requests and receive responses for BitPay's Web API
 *
 * @package Bitpay
 */
class Client extends ContainerAware implements ClientInterface
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function createInvoice(InvoiceInterface $invoice)
    {
        $this->logger->debug('Creating Invoice');
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPath('invoices');

        $currency     = $invoice->getCurrency();
        $item         = $invoice->getItem();
        $buyer        = $invoice->getBuyer();
        $buyerAddress = $buyer->getAddress();

        $body = array(
            'amount'             => $item->getPrice(),
            'currency'          => $currency->getCode(),
            'posData'           => $invoice->getPosData(),
            'notificationURL'   => $invoice->getNotificationUrl(),
            'transactionSpeed'  => $invoice->getTransactionSpeed(),
            'fullNotifications' => $invoice->isFullNotifications(),
            'notificationEmail' => $invoice->getNotificationEmail(),
            'redirectURL'       => $invoice->getRedirectUrl(),
            'orderID'           => $invoice->getOrderId(),
            'itemDesc'          => $item->getDescription(),
            'itemCode'          => $item->getCode(),
            'physical'          => $item->isPhysical(),
            'buyerName'         => trim(sprintf('%s %s', $buyer->getFirstName(), $buyer->getLastName())),
            'buyerAddress1'     => isset($buyerAddress[0]) ? $buyerAddress[0] : '',
            'buyerAddress2'     => isset($buyerAddress[1]) ? $buyerAddress[1] : '',
            'buyerCity'         => $buyer->getCity(),
            'buyerState'        => $buyer->getState(),
            'buyerZip'          => $buyer->getZip(),
            'buyerCountry'      => $buyer->getCountry(),
            'buyerEmail'        => $buyer->getEmail(),
            'buyerPhone'        => $buyer->getPhone(),
            'guid'              => Util::guid(),
            'nonce'             => Util::nonce(),
        );

        $request->setBody(json_encode($body));
        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);
        $this->logger->debug('Request', $body);
        $this->request  = $request;
        $this->response = $this->send($request);

        $body = json_decode($this->response->getBody(), true);
        $this->logger->debug('Response', $body);
        if (isset($body['error'])) {
            var_dump(
                $this->request,
                $this->response
            );
            throw new \Exception('Error with request');
        }
        $invoice
            ->setId($body['id'])
            ->setUrl($body['url'])
            ->setStatus($body['status'])
            ->setBtcPrice($body['btcPrice'])
            ->setPrice($body['price'])
            ->setInvoiceTime($body['invoiceTime'])
            ->setExpirationTime($body['expirationTime'])
            ->setCurrentTime($body['currentTime'])
            ->setBtcPaid($body['btcPaid'])
            ->setRate($body['rate'])
            ->setExceptionStatus($body['exceptionStatus']);

        return $invoice;
    }

    /**
     * @return array
     */
    public function getCurrencies()
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPath('currencies');
        $this->response = $this->send($request);
        $body           = json_decode($this->response->getBody(), true);
        $currencies     = $body['data'];
        array_walk($currencies, function (&$value, $key) {
            $currency = new \Bitpay\Currency();
            $currency
                ->setCode($value['code'])
                ->setSymbol($value['symbol'])
                ->setPrecision($value['precision'])
                ->setExchangePctFee($value['exchangePctFee'])
                ->setPayoutEnabled($value['payoutEnabled'])
                ->setName($value['name'])
                ->setPluralName($value['plural'])
                ->setAlts($value['alts'])
                ->setPayoutFields($value['payoutFields']);
            $value = $currency;
        });

        return $currencies;
    }

    public function createToken(array $payload = array())
    {
        $request = $this->createNewRequest();
        //$request->setHeader('X-Identity', $payload['id']);
        $request->setMethod(Request::METHOD_POST);
        $request->setPath('tokens');
        $payload['guid'] = Util::guid();
        //$payload['nonce'] = Util::nonce();
        $request->setBody(json_encode($payload));
        $this->logger->debug('Request', $payload);
        $this->response = $this->send($request);
        $body           = json_decode($this->response->getBody(), true);
        $this->logger->debug('Response', $body);

        return $body;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    protected function addIdentityHeader(RequestInterface $request)
    {
        $manager = $this->container->get('key_manager');
        $publicKey = $manager->load($this->container->getParameter('bitpay.public_key'));
        $sin = new \Bitpay\SinKey();
        $sin->setPublicKey($publicKey);
        $sin->generate();

        //$request->setHeader('x-identity', (string) $sin);
        $request->setHeader('x-identity', (string) $publicKey);
    }

    protected function addSignatureHeader(RequestInterface $request)
    {
        $manager    = $this->container->get('key_manager');
        $privateKey = $manager->load($this->container->getParameter('bitpay.private_key'));
        $bitauth    = new \Bitpay\Bitauth();
        $message    = sprintf(
            '%s%s',
            $request->getUri(),
            $request->getBody()
        );

        $signature = $bitauth->sign($message, $privateKey);

        $request->setHeader('x-signature', $signature);
    }

    /**
     * @return RequestInterface
     */
    protected function createNewRequest()
    {
        $request = new Request();
        $request->setHost($this->container->get('network')->getApiHost());

        // @see http://en.wikipedia.org/wiki/User_agent
        $request->setHeader(
            'User-Agent',
            sprintf('%s/%s (PHP %s)', self::NAME, self::VERSION, phpversion())
        );
        $request->setHeader('X-BitPay-Plugin-Info', sprintf('%s/%s', self::NAME, self::VERSION));
        $request->setHeader(
            'Authorization',
            sprintf('Basic %s', base64_encode($this->container->getParameter('bitpay.api_key')))
        );
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('X-Accept-Version', '2.0.0');

        return $request;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    protected function send(RequestInterface $request)
    {
        return $this->container->get('adapter')->sendRequest($request);
    }
}
