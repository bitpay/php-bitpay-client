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
class Token implements TokenInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var string
     */
    protected $facade;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var array
     */
    protected $policies;

    /**
     * @var string
     */
    protected $pairingCode;

    /**
     * @var \DateTime
     */
    protected $pairingExpiration;

    public function __construct()
    {
        $this->policies = array();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getToken();
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string
     * @return Token
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param string
     * @return Token
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacade()
    {
        return $this->facade;
    }

    /**
     * @param string
     * @return Token
     */
    public function setFacade($facade)
    {
        $this->facade = $facade;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime
     * @return Token
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return array
     */
    public function getPolicies()
    {
        return $this->policies;
    }

    /**
     * @param string
     * @return Token
     */
    public function setPolicies($policies)
    {
        $this->policies = $policies;

        return $this;
    }

    /**
     * @return string
     */
    public function getPairingCode()
    {
        return $this->pairingCode;
    }

    /**
     * @param string
     * @return Token
     */
    public function setPairingCode($pairingCode)
    {
        $this->pairingCode = $pairingCode;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPairingExpiration()
    {
        return $this->pairingExpiration;
    }

    /**
     * @param \DateTime
     * @return Token
     */
    public function setPairingExpiration(\DateTime $pairingExpiration)
    {
        $this->pairingExpiration = $pairingExpiration;

        return $this;
    }
}
