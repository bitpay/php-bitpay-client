<?php

namespace Bitpay;

/**
 * Object to represent a point on an elliptic curve
 *
 * @package Bitcore
 */
class Point implements PointInterface
{

    /**
     * MUST be a HEX value
     *
     * @var string
     */
    protected $x;

    /**
     * MUST be a HEX value
     *
     * @var string
     */
    protected $y;

    /**
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('(%s, %s)', $this->x, $this->y);
    }

    /**
     * @return string
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return string
     */
    public function getY()
    {
        return $this->y;
    }
}
