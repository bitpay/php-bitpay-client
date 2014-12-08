<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

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
     */
    public function __construct()
    {
        $this->policies = array();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getToken();
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param $token
     *
     * @return $this
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
     * @param $resource
     *
     * @return $this
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
     * @param $facade
     *
     * @return $this
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
     * @param $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
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
     * @param $policies
     *
     * @return $this
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
     * @param $pairingCode
     *
     * @return $this
     */
    public function setPairingCode($pairingCode)
    {
        $this->pairingCode = $pairingCode;
        
        return $this;
    }
}
