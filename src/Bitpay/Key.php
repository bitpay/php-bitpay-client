<?php

namespace Bitpay;

/**
 * Abstract object that is used for Public, Private, and SIN keys
 *
 * @package Bitcore
 */
abstract class Key extends Point implements KeyInterface
{

    /**
     * @var string
     */
    protected $hex;

    /**
     * @var string
     */
    protected $dec;

    /**
     * @var string
     */
    protected $id;

    /**
     */
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a new instance of self.
     *
     * @return \Bitpay\KeyInterface
     */
    public static function create()
    {
        $class = get_called_class();
        return new $class();
    }

    /**
     * @return string
     */
    public function getHex()
    {
        return $this->hex;
    }

    /**
     * @return string
     */
    public function getDec()
    {
        return $this->dec;
    }

    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->x,
                $this->y,
                $this->hex,
                $this->dec
            )
        );
    }

    public function unserialize($data)
    {
        list(
            $this->id,
            $this->x,
            $this->y,
            $this->hex,
            $this->dec
        ) = unserialize($data);
    }
}
