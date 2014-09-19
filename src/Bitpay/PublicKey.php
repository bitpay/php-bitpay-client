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
            'x' => sprintf('0x%s', substr(Secp256k1::G, 0, 62)),
            'y' => sprintf('0x%s', substr(Secp256k1::G, 62, 62)),
        );

        $R     = Gmp::doubleAndAdd('0x'.$this->privateKey->getHex(), $P, '0x'.Secp256k1::P, '0x'.Secp256k1::A);
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
        $this->dec = Util::decodeHex(sprintf('04%s%s', $R['x'], $R['y']));

        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return true;
    }
}
