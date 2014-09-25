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
use Bitpay\Util\SecureRandom;

/**
 * @package Bitcore
 * @see https://en.bitcoin.it/wiki/List_of_address_prefixes
 */
class PrivateKey extends Key
{
    /**
     * @var PublicKey
     */
    protected $publicKey;

    /**
     * @var boolean
     */
    protected $generated = false;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->hex;
    }

    /**
     * @return PublicKey
     */
    public function getPublicKey()
    {
        if (null === $this->publicKey) {
            $this->publicKey = new PublicKey();
            $this->publicKey->setPrivateKey($this);
            $this->publicKey->generate();
        }

        return $this->publicKey;
    }

    /**
     * Generates an EC private key
     *
     * @return \Bitpay\PrivateKey
     */
    public function generate()
    {
        if ($this->isGenerated()) {
            return $this;
        }

        do {
            $privateKey = \Bitpay\Util\SecureRandom::generateRandom(32);
            $this->hex  = strtolower(bin2hex($privateKey));
        } while (gmp_cmp('0x'.$this->hex, '1') <= 0 || gmp_cmp('0x'.$this->hex, '0x'.Secp256k1::N) >= 0);

        $this->dec = Util::decodeHex($this->hex);

        $this->generated = true;

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
    public function sign($data)
    {
        if ($this->isGenerated()) {
            $this->generate();
        }

        if (!ctype_xdigit($this->getHex())) {
            throw new \Exception('The private key must be in hex format.');
        }

        if (empty($data)) {
            throw new \Exception('You did not provide any data to sign.');
        }

        $e = Util::decodeHex(hash('sha256', $data));

        do {
            if (substr(strtolower($this->getHex()), 0, 2) != '0x') {
                $d = '0x'.$this->getHex();
            } else {
                $d = $this->getHex();
            }

            $k = SecureRandom::generateRandom(32);

            $k_hex = '0x'.strtolower(bin2hex($k));
            $n_hex = '0x'.Secp256k1::N;
            $a_hex = '0x'.Secp256k1::A;
            $p_hex = '0x'.Secp256k1::P;

            $Gx = '0x'.substr(Secp256k1::G, 0, 62);
            $Gy = '0x'.substr(Secp256k1::G, 64, 62);

            $P = new Point($Gx, $Gy);

            // Calculate a new curve point from Q=k*G (x1,y1)
            $R = Gmp::doubleAndAdd($k_hex, $P);

            $Rx_hex = Util::encodeHex($R->getX());
            $Ry_hex = Util::encodeHex($R->getY());

            while (strlen($Rx_hex) < 64) {
                $Rx_hex = '0'.$Rx_hex;
            }

            while (strlen($Ry_hex) < 64) {
                $Ry_hex = '0'.$Ry_hex;
            }

            // r = x1 mod n
            $r = gmp_strval(gmp_mod('0x'.$Rx_hex, $n_hex));

            // s = k^-1 * (e+d*r) mod n
            $edr = gmp_add($e, gmp_mul($d, $r));
            $invk = gmp_invert($k_hex, $n_hex);
            $kedr = gmp_mul($invk, $edr);
            $s = gmp_strval(gmp_mod($kedr, $n_hex));

            // The signature is the pair (r,s)
            $signature = array(
                'r' => Util::encodeHex($r),
                's' => Util::encodeHex($s),
            );

            while (strlen($signature['r']) < 64) {
                $signature['r'] = '0'.$signature['r'];
            }

            while (strlen($signature['s']) < 64) {
                $signature['s'] = '0'.$signature['s'];
            }
        } while (gmp_cmp($r, '0') <= 0 || gmp_cmp($s, '0') <= 0);

        $sig = array(
            'sig_rs' => $signature,
            'sig_hex' => self::serializeSig($signature['r'], $signature['s']),
        );

        return $sig['sig_hex']['seq'];
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

            $k = SecureRandom::generateRandom(32);

            $kHex = '0x'.strtolower(bin2hex($k));

            $gX   = '0x'.substr(Secp256k1::G, 0, 62);
            $gY   = '0x'.substr(Secp256k1::G, 64, 62);

            $p = new Point($gX, $gY);
            $r = Gmp::doubleAndAdd($this->hex, $p);

            $rXHex = Util::encodeHex($r->getX());
            $rYHex = Util::encodeHex($r->getY());

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

    public function isGenerated()
    {
        return $this->generated;
    }

    /**
     * ASN.1 DER encodes the signature based on the form:
     * 0x30 + size(all) + 0x02 + size(r) + r + 0x02 + size(s) + s
     * http://www.itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf
     *
     * @param string
     * @param string
     * @return string
     */
    public static function serializeSig($r, $s)
    {
        for ($x = 0; $x < 256; $x++) {
            $digits[$x] = chr($x);
        }

        $dec = Util::decodeHex($r);

        $byte = '';
        $seq = '';
        $retval = array();

        while (gmp_cmp($dec, '0') > 0) {
            $dv = gmp_div($dec, '256');
            $rem = gmp_strval(gmp_mod($dec, '256'));
            $dec = $dv;
            $byte = $byte.$digits[$rem];
        }

        $byte = strrev($byte);

        // msb check
        if (gmp_cmp('0x'.bin2hex($byte[0]), '0x80') >= 0) {
            $byte = chr(0x00).$byte;
        }

        $retval['bin_r'] = bin2hex($byte);
        $seq = chr(0x02).chr(strlen($byte)).$byte;
        $dec = Util::decodeHex($s);

        $byte = '';

        while (gmp_cmp($dec, '0') > 0) {
            $dv = gmp_div($dec, '256');
            $rem = gmp_strval(gmp_mod($dec, '256'));
            $dec = $dv;
            $byte = $byte.$digits[$rem];
        }

        $byte = strrev($byte);

        // msb check
        if (gmp_cmp('0x'.bin2hex($byte[0]), '0x80') >= 0) {
            $byte = chr(0x00).$byte;
        }

        $retval['bin_s'] = bin2hex($byte);
        $seq = $seq.chr(0x02).chr(strlen($byte)).$byte;
        $seq = chr(0x30).chr(strlen($seq)).$seq;
        $retval['seq'] = bin2hex($seq);

        return $retval;
    }
}
