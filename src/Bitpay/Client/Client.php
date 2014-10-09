<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

use Bitpay\Client\Adapter\AdapterInterface;
use Bitpay\Network\NetworkInterface;
use Bitpay\TokenInterface;
use Bitpay\InvoiceInterface;
use Bitpay\Util\Util;
use Bitpay\PublicKey;
use Bitpay\PrivateKey;

/**
 * Client used to send requests and receive responses for BitPay's Web API
 *
 * @package Bitpay
 */
class Client implements ClientInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var TokenInterface
     */
    protected $token;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var PublicKey
     */
    protected $publicKey;

    /**
     * @var PrivateKey
     */
    protected $privateKey;

    /**
     * @var NetworkInterface
     */
    protected $network;

    /**
     * The network is either livenet or testnet and tells the client where to
     * send the requests.
     *
     * @param NetworkInterface
     */
    public function setNetwork(NetworkInterface $network)
    {
        $this->network = $network;
    }

    /**
     * Set the Public Key to use to help identify who you are to BitPay. Please
     * note that you must first pair your keys and get a token in return to use.
     *
     * @param PublicKey $key
     */
    public function setPublicKey(PublicKey $key)
    {
        $this->publicKey = $key;
    }

    /**
     * Set the Private Key to use, this is used when signing request strings
     *
     * @param PrivateKey $key
     */
    public function setPrivateKey(PrivateKey $key)
    {
        $this->privateKey = $key;
    }

    /**
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param TokenInterface $token
     * @return ClientInterface
     */
    public function setToken(TokenInterface $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createInvoice(InvoiceInterface $invoice)
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPath('invoices');

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
            'guid'              => Util::guid(),
            'nonce'             => Util::nonce(),
            'token'             => $this->token->getToken(),
        );

        $request->setBody(json_encode($body));
        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);
        $this->request  = $request;
        $this->response = $this->sendRequest($request);

        $body = json_decode($this->response->getBody(), true);
        if (isset($body['error']) || isset($body['errors'])) {
            throw new \Exception('Error with request');
        }
        $data = $body['data'];
        $invoice
            ->setId($data['id'])
            ->setUrl($data['url'])
            ->setStatus($data['status'])
            ->setBtcPrice($data['btcPrice'])
            ->setPrice($data['price'])
            ->setInvoiceTime($data['invoiceTime'])
            ->setExpirationTime($data['expirationTime'])
            ->setCurrentTime($data['currentTime'])
            ->setBtcPaid($data['btcPaid'])
            ->setRate($data['rate'])
            ->setExceptionStatus($data['exceptionStatus']);

        return $invoice;
    }

    /**
     * Returns an array of Currency objects
     *
     * @return array
     */
    public function getCurrencies()
    {
        $this->request = $this->createNewRequest();
        $this->request->setMethod(Request::METHOD_GET);
        $this->request->setPath('currencies');
        $this->response = $this->sendRequest($this->request);
        $body           = json_decode($this->response->getBody(), true);
        if (empty($body['data'])) {
            throw new \Exception('Error with request');
        }
        $currencies = $body['data'];
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

    /**
     * Used to create a token. These method needs to be refactored to
     * work better
     *
     * @return TokenInterface
     */
    public function createToken(array $payload = array())
    {
        $this->request = $this->createNewRequest();
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setPath('tokens');
        $payload['guid'] = Util::guid();
        $this->request->setBody(json_encode($payload));
        $this->response = $this->sendRequest($this->request);
        $body           = json_decode($this->response->getBody(), true);

        if (isset($body['error'])) {
            throw new \Exception($body['error']);
        }

        $tkn = $body['data'][0];

        $token = new \Bitpay\Token();
        $token
            ->setPolicies($tkn['policies'])
            ->setResource($tkn['resource'])
            ->setToken($tkn['token'])
            ->setFacade($tkn['facade'])
            ->setCreatedAt($tkn['dateCreated']);

        return $token;
    }

    /**
     * Returns the Response object that BitPay returned from the request that
     * was sent
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns the request object that was sent to BitPay
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param  string           $invoiceId
     * @return InvoiceInterface
     */
    public function getInvoice($invoiceId)
    {
        $this->request = $this->createNewRequest();
        $this->request->setMethod(Request::METHOD_GET);
        $this->request->setPath(sprintf('invoices/%s', $invoiceId));
        $body = array(
            'guid'  => Util::guid(),
            'nonce' => Util::nonce(),
        );
        $this->request->setBody(json_encode($body));
        $this->response = $this->sendRequest($this->request);
        $body = json_decode($this->response->getBody(), true);

        if (isset($body['error'])) {
            throw new \Exception($body['error']);
        }

        return $body['data'];
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request)
    {
        if (null === $this->adapter) {
            // Uses the default adapter
            $this->adapter = new \Bitpay\Client\Adapter\CurlAdapter();
        }

        return $this->adapter->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     */
    protected function addIdentityHeader(RequestInterface $request)
    {
        if (null === $this->publicKey) {
            throw new \Exception('Please set your Public Key.');
        }

        $request->setHeader('x-identity', (string) $this->publicKey);
    }

    /**
     * @param RequestInterface $request
     */
    protected function addSignatureHeader(RequestInterface $request)
    {
        if (null === $this->privateKey) {
            throw new \Exception('Please set your Private Key');
        }

        $message = sprintf(
            '%s%s',
            $request->getUri(),
            $request->getBody()
        );

        $signature = $this->privateKey->sign($message);

        $request->setHeader('x-signature', $signature);
    }

    /**
     * @return RequestInterface
     */
    protected function createNewRequest()
    {
        $request = new Request();
        $request->setHost($this->network->getApiHost());
        $this->prepareRequestHeaders($request);

        return $request;
    }

    /**
     * Prepares the request object by adding additional headers
     *
     * @param RequestInterface $request
     */
    protected function prepareRequestHeaders(RequestInterface $request)
    {
        // @see http://en.wikipedia.org/wiki/User_agent
        $request->setHeader(
            'User-Agent',
            sprintf('%s/%s (PHP %s)', self::NAME, self::VERSION, phpversion())
        );
        $request->setHeader('X-BitPay-Plugin-Info', sprintf('%s/%s', self::NAME, self::VERSION));
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('X-Accept-Version', '2.0.0');
    }
}
