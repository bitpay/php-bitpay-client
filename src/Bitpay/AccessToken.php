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
     * @param string $email
     *
     * @return AccessTokenInterface
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
     * @param string $label
     *
     * @return AccessTokenInterface
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
