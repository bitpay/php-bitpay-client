<?php

namespace Bitpay;

/**
 * @package Bitpay
 */
class AccessToken implements AccessTokenInterface
{

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var boolean
     */
    protected $useNonce;

    /**
     */
    public function __construct()
    {
        /**
         * Set various defaults for this object.
         */
        $this->useNonce = true;
    }

    /**
     * @param string $id
     *
     * @return AccessTokenInterface
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @param string $email
     *
     * @return AccessTokenInterface
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $label
     *
     * @return AccessTokenInterface
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function isNonceDisabled()
    {
        return !($this->useNonce);
    }

    /**
     * Enable nonce usage
     *
     * @return AccessTokenInterface
     */
    public function nonceEnable()
    {
        $this->useNonce = true;

        return $this;
    }

    /**
     * Disable nonce usage
     *
     * @return AccessTokenInterface
     */
    public function nonceDisable()
    {
        $this->useNonce = false;

        return $this;
    }
}
