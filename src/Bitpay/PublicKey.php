<?php

namespace Bitpay;

use Bitpay\Util\Base58;
use Bitpay\Util\Gmp;
use Bitpay\Util\Secp256k1;
use Bitpay\Util\Util;

/**
 * @package Bitcore
 */
class PublicKey extends Key
{

    /**
     * @var PrivateKey
     */
    protected $privateKey;

    /**
     * Returns compressed key value
     *
     * @return string
     */
    public function __toString()
    {
        if (is_null($this->x)) {
            return '';
        }

        return sprintf('02%s', $this->x);
    }

    /**
     * @return KeyInterface
     */
    public function setPrivateKey(PrivateKey $privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Generates a Public Key
     *
     * @param \Bitpay\PrivateKey $privateKey
     *
     * @return Bitpay\PublicKey
     */
    public function generate()
    {
        if (is_null($this->privateKey)) {
            throw new \Exception('Please `setPrivateKey` before you generate a public key');
        }

        if (!$this->privateKey->isValid()) {
            throw new \Exception('Private Key is invalid and cannot be used to generate a public key');
        }

        $P = array(
            'x' => sprintf('0x%s', substr(Secp256k1::G, 2, 64)),
            'y' => sprintf('0x%s', substr(Secp256k1::G, 66, 64)),
        );

        $R     = Gmp::doubleAndAdd($this->privateKey->getHex(), $P, Secp256k1::P, Secp256k1::A);
        $RxHex = Util::encodeHex($R['x']);
        $RyHex = Util::encodeHex($R['y']);
        while (strlen($RxHex) < 64) {
            $RxHex .= 0;
        }
        while (strlen($RyHex) < 64) {
            $RyHex .= 0;
        }

        $this->x   = $RxHex;
        $this->y   = $RyHex;
        $this->hex = sprintf('04%s%s', $RxHex, $RyHex);
        $this->dec = sprintf(sprintf('04%s%s', $R['x'], $R['y']));

        return $this;
    }

    public function isValid()
    {
        return true;
    }
}
