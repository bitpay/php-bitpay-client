<?php

namespace Bitpay;

interface RateInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     *
     * @return RateInterface
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return RateInterface
     */
    public function setName($name);

    /**
     * @return float
     */
    public function getRate();

    /**
     * @param float $rate
     *
     * @return RateInterface
     */
    public function setRate($rate);
}
