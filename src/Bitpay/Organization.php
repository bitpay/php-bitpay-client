<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */
namespace Bitpay;


class Organization implements OrganizationInterface
{

    protected $name;

    protected $address;

    protected $city;

    protected $state;

    protected $zip;

    protected $country;

    protected $isNonProfit;

    protected $usTaxId;

    protected $industry;

    protected $website;

    protected $cartPos;

    protected $affiliateOId;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param $name
     *
     * @return OrganizationInterface
     */
    public function setName($name)
    {
        if (!empty($name) && is_string($name) && ctype_print($name)) {
            $this->name = trim($name);
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
        if (!empty($address) && is_array($address)) {
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
     * @inheritdoc
     */
    public function getIsNonProfit()
    {
        return $this->isNonProfit;
    }

    /**
     * @param bool $boolValue
     *
     * @return User
     */
    public function setIsNonProfit($boolValue)
    {
        if (!empty($boolValue)) {
            $this->isNonProfit = $boolValue;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUSTaxId()
    {
        return $this->usTaxId;
    }

    /**
     * @param string $usTaxId
     *
     * @return UserInterface
     */
    public function setUSTaxId($usTaxId)
    {
        if (!empty($usTaxId) && is_string($usTaxId) && ctype_print($usTaxId)) {
            $this->usTaxId = trim($usTaxId);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @param string $industry
     *
     * @return UserInterface
     */
    public function setIndustry($industry)
    {
        if (!empty($industry) && is_string($industry) && ctype_print($industry)) {
            $this->industry = trim($industry);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     *
     * @return UserInterface
     */
    public function setWebsite($website)
    {
        if (!empty($website) && is_string($website) && ctype_print($website)) {
            $this->website = trim($website);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCartPOS()
    {
        return $this->cartPos;
    }

    /**
     * @param string $cartPOS
     *
     * @return UserInterface
     */
    public function setCartPOS($cartPOS)
    {
        if (!empty($cartPOS) && is_string($cartPOS) && ctype_print($cartPOS)) {
            $this->cartPos = trim($cartPOS);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAffiliateOID()
    {
        return $this->affiliateOId;
    }

    /**
     * @param string $affiliateOID
     *
     * @return UserInterface
     */
    public function setAffiliateOID($affiliateOID)
    {
        if (!empty($affiliateOID) && is_string($affiliateOID) && ctype_print($affiliateOID)) {
            $this->cartPos = trim($affiliateOID);
        }

        return $this;
    }
}