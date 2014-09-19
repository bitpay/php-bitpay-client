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
class User implements UserInterface
{
    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var array
     */
    protected $address;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var bool
     */
    protected $agreedToTOSandPP;

    /**
     * @inheritdoc
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return UserInterface
     */
    public function setPhone($phone)
    {
        if (!empty($phone) && is_string($phone) && ctype_print($phone)) {
            $this->phone = trim($phone);
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
     * @param string $email
     *
     * @return UserInterface
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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return UserInterface
     */
    public function setFirstName($firstName)
    {
        if (!empty($firstName) && is_string($firstName) && ctype_print($firstName)) {
            $this->firstName = trim($firstName);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return UserInterface
     */
    public function setLastName($lastName)
    {
        if (!empty($lastName) && is_string($lastName) && ctype_print($lastName)) {
            $this->lastName = trim($lastName);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param array $address
     *
     * @return UserInterface
     */
    public function setAddress(array $address)
    {
        if (!empty($address) && is_array($address) && ctype_print($address)) {
            $this->address = $address;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return UserInterface
     */
    public function setCity($city)
    {
        if (!empty($city) && is_string($city) && ctype_print($city)) {
            $this->city = trim($city);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return UserInterface
     */
    public function setState($state)
    {
        if (!empty($state) && is_string($state) && ctype_print($state)) {
            $this->state = trim($state);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     *
     * @return UserInterface
     */
    public function setZip($zip)
    {
        if (!empty($zip) && is_string($zip) && ctype_print($zip)) {
            $this->zip = trim($zip);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return UserInterface
     */
    public function setCountry($country)
    {
        if (!empty($country) && is_string($country) && ctype_print($country)) {
            $this->country = trim($country);
        }

        return $this;
    }

    /**
     * @param bool $boolvalue
     *
     * @return User
     */
    public function setAgreedToTOSandPP($boolvalue)
    {
        if (!empty($boolvalue)) {
            $this->agreedToTOSandPP = $boolvalue;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getAgreedToTOSandPP()
    {
        return $this->agreedToTOSandPP;
    }
}
