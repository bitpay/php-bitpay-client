<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

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
     * Set various defaults for this object.
     */
    public function __construct($id = null, $email = null, $label = null)
    {
        $this->id    = $id;
        $this->email = $email;
        $this->label = $label;
    }

    /**
     * Setter for the id.
     *
     * @param string $id
     * @return AccessToken
     */
    public function setId($id)
    {
        if (!empty($id) && is_string($id) && ctype_print($id)) {
            $this->id = trim($id);
        }

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
     * Setter for the email address.
     *
     * @param string $email
     * @return AccessToken
     */
    public function setEmail($email)
    {
        if (!empty($email) && is_string($email) && ctype_print($email)) {
            $this->email = trim($email);
        }

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
     * Setter for the label.
     *
     * @param string $label
     * @return AccessToken
     */
    public function setLabel($label)
    {
        if (!empty($label) && is_string($label) && ctype_print($label)) {
            $this->label = trim($label);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->label;
    }
}
