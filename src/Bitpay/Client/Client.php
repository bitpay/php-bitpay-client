<?php

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
        $request->setPath('api/invoice');

        $currency     = $invoice->getCurrency();
        $item         = $invoice->getItem();
        $buyer        = $invoice->getBuyer();
        $buyerAddress = $buyer->getAddress();

        $body = array(
                'price'             => $item->getPrice(),
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
        );

        $request->setBody(json_encode($body));
        $this->logger->debug('Request', $body);
        $this->response = $this->send($request);

        $body = json_decode($this->response->getBody(), true);
        $this->logger->debug('Response', $body);
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
