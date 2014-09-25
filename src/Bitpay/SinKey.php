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

use Bitpay\Util\Base58;
use Bitpay\Util\Gmp;
use Bitpay\Util\Util;

/**
 * @package Bitcore
 */
class SinKey extends Key
{
    // Type 2 (ephemeral)
    const SIN_TYPE    = '02';

    // Always the prefix!
    // (well, right now)
    const SIN_VERSION = '0F';

    /**
     * @var string
     */
    protected $value;

    /**
     * @var PublicKey
     */
    protected $publicKey;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @param PublicKey
     * @return SinKey
     */
    public function setPublicKey(PublicKey $publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Generates a Service Identification Number (SIN), see:
     * https://en.bitcoin.it/wiki/Identity_protocol_v1
     *
     * @return SinKey
     */
    public function generate()
    {
        if (is_null($this->publicKey)) {
            throw new \Exception('Public Key has not been set');
        }

        $compressedValue = $this->publicKey;

        if (empty($compressedValue)) {
            throw new \Exception('The Public Key needs to be generated.');
        }

        $step1 = Util::sha256(Gmp::gmpBinconv($compressedValue), true);

        $step2 = Util::ripe160($step1);

        $step3 = sprintf(
            '%s%s%s',
            self::SIN_VERSION,
            self::SIN_TYPE,
            $step2
        );

        $step4 = Util::twoSha256(Gmp::gmpBinconv($step3), true);

        $step5 = substr(bin2hex($step4), 0, 8);

        $step6 = $step3.$step5;

        $this->value = Base58::encode($step6);

        return $this;
    }

    /**
     * Checks to make sure that this SIN is a valid object.
     *
     * @return boolean
     */
    public function isValid()
    {
        return (!is_null($this->value) && (substr($this->value, 0, 1) == 'T'));
    }
}
