<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

date_default_timezone_set('UTC');

/**
 * @package Bitpay
 */
class Invoice implements InvoiceInterface
{
    /**
     * @var CurrencyInterface
     */
    protected $currency = null;

    /**
     * @var string
     */
    protected $orderId = '';

    /**
     * @var ItemInterface
     */
    protected $item = null;

    /**
     * @var string
     */
    protected $transactionSpeed = '';

    /**
     * @var string
     */
    protected $notificationEmail = '';

    /**
     * @var string
     */
    protected $notificationUrl = '';

    /**
     * @var string
     */
    protected $redirectUrl = '';

    /**
     * @var string
     */
    protected $posData = '';

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @var boolean
     */
    protected $fullNotifications = false;

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var float
     */
    protected $btcPrice = 0.000000;

    /**
     * @var \DateTime
     */
    protected $invoiceTime = null;

    /**
     * @var \DateTime
     */
    protected $expirationTime = null;

    /**
     * @var \DateTime
     */
    protected $currentTime = null;

    /**
     * @var BuyerInterface
     */
    protected $buyer = null;

    /**
     * @var string
     */
    protected $exceptionStatus = '';

    /**
     * @var float
     */
    protected $btcPaid = 0.000000;

    /**
     * @var float
     */
    protected $rate = 0.000000;

    /**
     * @var Token
     */
    protected $token = null;

    /**
     * @var PaymentUrlInterface
     */
    protected $paymentUrls = null;

    public function __construct($transactionSpeed = self::TRANSACTION_SPEED_LOW, $fullNotifications = false, $item = null, $currency = null, $orderId = '', $posData = '')
    {
        $this->currency = $currency;
        $this->transactionSpeed  = $transactionSpeed;
        $this->fullNotifications = $fullNotifications;
        $this->item = $item;
        $this->orderId = $orderId;
        $this->posData = $posData;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        if (is_a($this->item, '\Bitpay\Item')) {
            return $this->getItem()->getPrice();
        } else {
            return 0.000000;
        }
    }

    /**
     * @param float $price
     * @return Invoice
     */
    public function setPrice($price)
    {
        if (is_numeric($price)) {
            $this->getItem()->setPrice(floatval($price));
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        if ($this->currency === null) {
            $this->currency = new Currency('BTC');
        }

        return $this->currency;
    }

    /**
     * @param CurrencyInterface $currency
     * @return Invoice
     */
    public function setCurrency(CurrencyInterface $currency)
    {
        if (is_a($currency, '\Bitpay\Currency')) {
            $this->currency = $currency;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getItem()
    {
        if (null === $this->item) {
            $this->item = new Item();
        }

        return $this->item;
    }

    /**
     * @param ItemInterface $item
     * @return Invoice
     */
    public function setItem(ItemInterface $item)
    {
        if (is_a($item, '\Bitpay\Item')) {
            $this->item = $item;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBuyer()
    {
        if ($this->buyer === null) {
            $this->buyer = new Buyer();
        }

        return $this->buyer;
    }

    /**
     * @param BuyerInterface $buyer
     * @return Invoice
     */
    public function setBuyer(BuyerInterface $buyer)
    {
        if (is_a($buyer, '\Bitpay\Buyer')) {
            $this->buyer = $buyer;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTransactionSpeed()
    {
        return $this->transactionSpeed;
    }

    /**
     * @param string $transactionSpeed
     * @return Invoice
     */
    public function setTransactionSpeed($transactionSpeed)
    {
        switch (strtolower(trim($transactionSpeed))) {
            case 'high':
                $this->transactionSpeed = self::TRANSACTION_SPEED_HIGH;
                break;
            case 'medium':
                $this->transactionSpeed = self::TRANSACTION_SPEED_MEDIUM;
                break;
            case 'low':
            default:
                $this->transactionSpeed = self::TRANSACTION_SPEED_LOW;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNotificationEmail()
    {
        return $this->notificationEmail;
    }

    /**
     * @param string $notificationEmail
     * @return Invoice
     */
    public function setNotificationEmail($notificationEmail)
    {
        if (filter_var($notificationEmail, FILTER_VALIDATE_EMAIL)) {
            $this->notificationEmail = trim($notificationEmail);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNotificationUrl()
    {
        return $this->notificationUrl;
    }

    /**
     * @param string $notificationUrl
     * @return Invoice
     */
    public function setNotificationUrl($notificationUrl)
    {
        if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $notificationUrl)) {
            $this->notificationUrl = trim($notificationUrl);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     * @return Invoice
     */
    public function setRedirectUrl($redirectUrl)
    {
        if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $redirectUrl)) {
            $this->redirectUrl = trim($redirectUrl);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPosData()
    {
        return $this->posData;
    }

    /**
     * @param string $posData
     * @return Invoice
     */
    public function setPosData($posData)
    {
        if (is_string($posData)) {
            $this->posData = $posData;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Invoice
     */
    public function setStatus($status)
    {
        if (is_string($status)) {
            $this->status = trim($status);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isFullNotifications()
    {
        return $this->fullNotifications;
    }

    public function setFullNotifications($notifications)
    {
        $this->fullNotifications = (boolean) $notifications;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Invoice
     */
    public function setId($id)
    {
        if (is_string($id)) {
            $this->id = trim($id);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Invoice
     */
    public function setUrl($url)
    {
        if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
            $this->url = trim($url);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBtcPrice()
    {
        return $this->btcPrice;
    }

    /**
     * @param float $btcPrice
     * @return Invoice
     */
    public function setBtcPrice($btcPrice)
    {
        if (is_numeric($btcPrice)) {
            $this->btcPrice = floatval($btcPrice);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceTime()
    {
        return $this->invoiceTime;
    }

    /**
     * @param \DateTime $invoiceTime
     * @return Invoice
     */
    public function setInvoiceTime($invoiceTime)
    {
        if (is_a($invoiceTime, 'DateTime')) {
            $this->invoiceTime = $invoiceTime;
        } else if (is_numeric($invoiceTime)) {
            $invoiceDateTime = new \DateTime();
            $invoiceDateTime->setTimestamp($invoiceTime);
            $this->invoiceTime = $invoiceDateTime;
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * @param \DateTime $expirationTime
     * return Invoice
     */
    public function setExpirationTime($expirationTime)
    {
        if (is_a($expirationTime, 'DateTime')) {
            $this->expirationTime = $expirationTime;
        } else if (is_numeric($expirationTime)) {
            $expirationDateTime = new \DateTime();
            $expirationDateTime->setTimestamp($expirationTime);
            $this->expirationTime = $expirationDateTime;
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentTime()
    {
        return $this->currentTime;
    }

    /**
     * @param \DateTime $currentTime
     * @return Invoice
     */
    public function setCurrentTime($currentTime)
    {
        if (is_a($currentTime, 'DateTime')) {
            $this->currentTime = $currentTime;
        } else if (is_numeric($currentTime)) {
            $currentDateTime = new \DateTime();
            $currentDateTime->setTimestamp($currentTime);
            $this->currentTime = $currentDateTime;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     * @return Invoice
     */
    public function setOrderId($orderId)
    {
        if (is_string($orderId)) {
            $this->orderId = trim($orderId);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getItemDesc()
    {
        return $this->getItem()->getDescription();
    }

    /**
     * @inheritdoc
     */
    public function getItemCode()
    {
        return $this->getItem()->getCode();
    }

    /**
     * @inheritdoc
     */
    public function isPhysical()
    {
        return $this->getItem()->isPhysical();
    }

    /**
     * @inheritdoc
     */
    public function getBuyerName()
    {
        $firstName = $this->getBuyer()->getFirstName();
        $lastName  = $this->getBuyer()->getLastName();

        return trim($firstName . ' ' . $lastName);
    }

    /**
     * @inheritdoc
     */
    public function getBuyerAddress1()
    {
        $address = $this->getBuyer()->getAddress();

        return $address[0];
    }

    /**
     * @inheritdoc
     */
    public function getBuyerAddress2()
    {
        $address = $this->getBuyer()->getAddress();

        return $address[1];
    }

    /**
     * @inheritdoc
     */
    public function getBuyerCity()
    {
        return $this->getBuyer()->getCity();
    }

    /**
     * @inheritdoc
     */
    public function getBuyerState()
    {
        return $this->getBuyer()->getState();
    }

    /**
     * @inheritdoc
     */
    public function getBuyerZip()
    {
        return $this->getBuyer()->getZip();
    }

    /**
     * @inheritdoc
     */
    public function getBuyerCountry()
    {
        return $this->getBuyer()->getCountry();
    }

    /**
     * @inheritdoc
     */
    public function getBuyerEmail()
    {
        return $this->getBuyer()->getEmail();
    }

    /**
     * @inheritdoc
     */
    public function getBuyerPhone()
    {
        return $this->getBuyer()->getEmail();
    }

    /**
     * @inheritdoc
     */
    public function getExceptionStatus()
    {
        return $this->exceptionStatus;
    }

    /**
     * @param
     * @return Invoice
     */
    public function setExceptionStatus($exceptionStatus)
    {
        $this->exceptionStatus = $exceptionStatus;
        return $this;
    }

    /**
     * @param void
     * @return
     */
    public function getBtcPaid()
    {
        return $this->btcPaid;
    }

    /**
     * @param
     * @return Invoice
     */
    public function setBtcPaid($btcPaid)
    {
        if (is_numeric($btcPaid)) {
            $this->btcPaid = $btcPaid;
        }

        return $this;
    }

    /**
     * @param void
     * @return Invoice
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param
     * @return
     */
    public function setRate($rate)
    {
        if (is_numeric($rate)) {
            $this->rate = floatval($rate);
        }

        return $this;
    }

    /**
     * @return TokenInterface
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param TokenInterface $token
     * @return Invoice
     */
    public function setToken(TokenInterface $token)
    {
        if (is_a($token, '\Bitpay\Token')) {
            $this->token = $token;
        }

        return $this;
    }

    /**
     * @return PaymentUrlInterface
     */
    public function getPaymentUrls()
    {
        if ($this->paymentUrls === null) {
            $this->paymentUrls = new PaymentUrlSet();
        }

        return $this->paymentUrls;
    }

    /**
     * @param PaymentUrlInterface $paymentUrls
     * @return Invoice
     */
    public function setPaymentUrls(PaymentUrlInterface $paymentUrls)
    {
        $this->paymentUrls = $paymentUrls;

        return $this;
    }

    /**
     * @param string $paymentUrlType
     * @return string
     */
    public function getPaymentUrl($paymentUrlType)
    {
        return $this->getPaymentUrls()->getUrl($paymentUrlType);
    }
}
