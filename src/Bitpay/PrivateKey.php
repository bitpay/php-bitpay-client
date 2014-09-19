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

use Bitpay\Util\Secp256k1;
use Bitpay\Util\Gmp;
use Bitpay\Util\Util;

/**
 * @package Bitcore
 * @see https://en.bitcoin.it/wiki/List_of_address_prefixes
 */
class PrivateKey extends Key
{
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->hex;
    }

    /**
     * Generates an EC private key
     *
     * @return \Bitpay\PrivateKey
     */
    public function generate()
    {
        do {
            $privateKey = openssl_random_pseudo_bytes(32, $cstrong);

            if (!$privateKey || !$cstrong) {
                throw new \Exception(
                    'Could not generate a cryptographically-strong random number. Your OpenSSL extension might be old or broken.'
                );
            }

            $this->hex  = strtolower(bin2hex($privateKey));
        } while (gmp_cmp('0x'.$this->hex, '1') <= 0 || gmp_cmp('0x'.$this->hex, '0x'.Secp256k1::N) >= 0);

        $this->dec = Util::decodeHex($this->hex);

        $this->x = substr($this->hex, 0, 32);
        $this->y = substr($this->hex, 32, 64);

        return $this;
    }

    /**
     * Checks to see if the private key value is not empty and
     * the hex form only contains hexits and the decimal form
     * only contains devimal digits.
     *
     * @return boolean
     */
    public function isValid()
    {
        return ($this->hasValidDec() && $this->hasValidHex());
    }

    /**
     * @return boolean
     */
    public function hasValidHex()
    {
        return (!empty($this->hex) || ctype_xdigit($this->hex));
    }

    /**
     * @return boolean
     */
    public function hasValidDec()
    {
        return (!empty($this->dec) || ctype_digit($this->dec));
    }

    /**
     * Creates an ECDSA signature of $message
     *
     * @return string
     */
    public function sign($message)
    {
        // TODO: Signature code is already in Bitauth so is this needed here?

        if (empty($message)) {
            throw new \Exception('You did not provide a message to hash.');
        }

        $e = Util::decodeHex(hash('sha256', $message));

        do {
            if (substr(strtolower($this->hex), 0, 2) != '0x') {
                $d = '0x'.$this->hex;
            } else {
                $d = $this->hex;
            }

            $k    = openssl_random_pseudo_bytes(32);

            if (!$k || !$cstrong) {
                // TODO: Throw exception
                throw new \Exception(
                    'Could not generate a cryptographically-strong random number. Your OpenSSL extension might be old or broken.'
                );
            }

            $kHex = '0x'.strtolower(bin2hex($k));

            $gX   = '0x'.substr(Secp256k1::G, 0, 62);
            $gY   = '0x'.substr(Secp256k1::G, 64, 62);

            $p    = array(
                          'x' => $gX,
                          'y' => $gY,
                         );

            $r     = Gmp::doubleAndAdd($this->hex, $p, '0x'.Secp256k1::P, '0x'.Secp256k1::A);

            $rXHex = Util::encodeHex($r['x']);
            $rYHex = Util::encodeHex($r['y']);

            while (strlen($rXHex) < 64) {
                $rXHex = '0'.$rXHex;
            }

            while (strlen($rYHex) < 64) {
                $rYHex = '0'.$rYHex;
            }

            $r2   = gmp_strval(gmp_mod('0x'.$rXHex, '0x'.Secp256k1::N));
            $edr  = gmp_add($e, gmp_mul($d, $r2));
            $invk = gmp_invert($kHex, '0x'.Secp256k1::N);
            $kedr = gmp_mul($invk, $edr);
            $s    = gmp_strval(gmp_mod($kedr, '0x'.Secp256k1::N));

            $signature = array(
                               'r' => Util::encodeHex($r2),
                               's' => Util::encodeHex($s),
                              );

            while (strlen($signature['r']) < 64) {
                $signature['r'] = '0'.$signature['r'];
            }

            while (strlen($signature['s']) < 64) {
                $signature['s'] = '0'.$signature['s'];
            }
        } while (gmp_cmp($r2, '0') <= 0 || gmp_cmp($s, '0') <= 0);

        return $signature;
    }
}
