<?php namespace BitPay;

use BitPay\Hash;
use BitPay\Request\Curl as Request;

class BitPay
{
    /**
     * API Key for Requests
     * @var string
     */
    protected $apiKey;

    /**
     * Request dependency
     * @var BitPay\Request
     */
    protected $request;

    /**
     * Hasher dependency
     * @var Bitpay\Hasher
     */
    protected $hasher;

    /**
     * Default options
     * @var array
     */
    public $options = array(
        'verifyPos' => true,
        'notificationEmail' => '',
        'notificationURL' => '',
        'redirectURL' => '',
        'currency' => 'BTC',
        'physical' => 'true',
        'fullNotifications' => 'true',
        'transactionSpeed' => 'low'
    );

    /**
     * Construct class. Set Request and Hash service.
     * @param Request $request
     * @param Hash $hasher
     * @param string $key API key
     * @param array $options Default Options
     */
    public function __construct(Request $request, Hash $hasher, $key = null, $options = array())
    {
        $this->request = $request;
        $this->hasher = $hasher;
        $this->setApiKey($key);
        $this->setOptions($options);
    }

    /**
     * Set the API Key. Chainable.
     * @param string $key
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;
        return $this;
    }

    /**
     * Set API options. Chainable.
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Fetch Invoice information
     * @param string $orderID Used to display an orderID to the buyer. In the account summary view,
     *        this value is used to identify a ledger entry if present.
     * @return array Invoice data
     */
    public function getInvoice($orderID)
    {
        $response = $this->request->get('invoice/' . $orderID, $this->apiKey);
        return (object) $response;
    }

    /**
     * Create BitPay Invoice
     * @param int $orderId Used to display an orderID to the buyer.
     *        In the account summary view, this value is used to identify a ledger entry if present.
     * @param float $price By default, price is expressed in the currency you set options.
     * @param array $posData This field is included in status updates or requests to get an invoice.
     *        It is intended to be used by the merchant to uniquely identify an order associated with
     *        an invoice in their system. Aside from that, Bit-Pay does not use the data in this field.
     *        The data in this field can be anything that is meaningful to the merchant.
     * @param array $options Keys can include any of: itemDesc, itemCode, notificationEmail, notificationURL,
     *        redirectURL, apiKey, currency, physical, fullNotifications, transactionSpeed, buyerName,
     *        buyerAddress1, buyerAddress2, buyerCity, buyerState, buyerZip, buyerEmail, buyerPhone
     * @see https://bitpay.com/bitcoin-payment-gateway-api
     * @return object Response
     */
    public function createInvoice($orderId, $price, $posData = array(), $options = array())
    {
        $options = array_merge($this->options, $options);

        $pos = array('posData' => $posData);

        if ($options['verifyPos']) {
            $pos['hash'] = $this->hasher->encrypt(
                serialize($posData),
                $this->apiKey
            );
        }

        $options['posData'] = json_encode($pos);
        $options['orderID'] = $orderId;
        $options['price'] = $price;

        $postOptions = array(
            'orderID',
            'itemDesc',
            'itemCode',
            'notificationEmail',
            'notificationURL',
            'redirectURL',
            'posData',
            'price',
            'currency',
            'physical',
            'fullNotifications',
            'transactionSpeed',
            'buyerName',
            'buyerAddress1',
            'buyerAddress2',
            'buyerCity',
            'buyerState',
            'buyerZip',
            'buyerEmail',
            'buyerPhone'
        );

        foreach ($postOptions as $o) {
            if (array_key_exists($o, $options)) {
                $post[$o] = $options[$o];
            }
        }

        $post = json_encode($post);

        $response = $this->request->post('invoice/', $post, $this->apiKey);

        return (object) $response;
    }

    /**
     * Call from your notification handler to convert $_POST data to an object containing invoice data
     * @param string $_POST data
     * @return object Invoice
     */
    public function verifyNotification($postData)
    {
        $json = json_decode($postData, true);

        if (is_string($json)) {
            return $json; // error
        }

        if (!array_key_exists('posData', $json)) {
            return 'no posData';
        }

        $posData = json_decode($json['posData'], true);

        if ($this->options['verifyPos']) {
            $hashCheck = $this->hasher->encrypt(serialize($posData['posData']), $this->apiKey);
            if ($posData['hash'] != $hashCheck) {
                return 'authentication failed (bad hash)';
            }
        }

        $json['posData'] = $posData['posData'];

        return (object) $json;
    }
}
