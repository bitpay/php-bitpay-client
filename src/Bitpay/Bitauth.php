<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

use Bitpay\Util\SecureRandom;
use Bitpay\Util\Util;
use Bitpay\Util\Gmp;
use Bitpay\Util\Secp256k1;

/**
 * This provides an easy interface for implementing Bitauth
 *
 * @package Bitauth
 */
class Bitauth
{

    /**
     */
    public function __construct()
    {
        if (!function_exists('openssl_open')) {
            throw new Exception('Your OpenSSL implementation is either too old or broken. Please contact your server administrator.');
        }

        if (!function_exists('mcrypt_list_algorithms')) {
            throw new Exception('Missing mcrypt PHP extension. Cannot continue.');
        }
    }

    /**
     * Generate Service Identification Number (SIN)
     *
     * @param void
     * @return array
     */
    public function generateSin()
    {
        $priKey = PrivateKey::create()->generate();
        $pubKey = PublicKey::create()->setPrivateKey($priKey)->generate();
        $sinKey = SinKey::create()->setPublicKey($pubKey)->generate();

        return array(
            'public'  => $pubKey,
            'private' => $priKey,
            'sin'     => $sinKey,
        );
    }

    /**
     * @return PublicKey
     */
    public function getPublicKeyFromPrivateKey(\Bitpay\PrivateKey $privateKey)
    {
        return PublicKey::create()->generate($privateKey);
    }

    /**
     * @return Sin
     */
    public function getSinFromPublicKey(\Bitpay\PublicKey $publicKey)
    {
        // TODO
        //return Sin::fromPubKey($publicKey);
    }

    /**
     * Generates an ECDSA signature of $data
     *
     * @param  string     $data
     * @param  PrivateKey $privateKey
     * @return string
     */
    public function sign($data, \Bitpay\PrivateKey $privateKey)
    {
        return $privateKey->sign($data);

        //if (!ctype_xdigit($privateKey->getHex())) {
        //    throw new \Exception('The private key must be in hex format.');
        //}

        //if (empty($data)) {
        //    throw new \Exception('You did not provide any data to sign.');
        //}

        //$e = Util::decodeHex(hash('sha256', $data));

        //do {
        //    if (substr(strtolower($privateKey->getHex()), 0, 2) != '0x') {
        //        $d = '0x'.$privateKey->getHex();
        //    } else {
        //        $d = $privateKey->getHex();
        //    }

        //    $k = SecureRandom::generateRandom(32);

        //    $k_hex = '0x'.strtolower(bin2hex($k));
        //    $n_hex = '0x'.Secp256k1::N;
        //    $a_hex = '0x'.Secp256k1::A;
        //    $p_hex = '0x'.Secp256k1::P;

        //    $Gx = '0x'.substr(Secp256k1::G, 0, 62);
        //    $Gy = '0x'.substr(Secp256k1::G, 64, 62);

        //    $P = new Point($Gx, $Gy);

        //    // Calculate a new curve point from Q=k*G (x1,y1)
        //    $R = Gmp::doubleAndAdd($k_hex, $P);

        //    $Rx_hex = Util::encodeHex($R->getX());
        //    $Ry_hex = Util::encodeHex($R->getY());

        //    while (strlen($Rx_hex) < 64) {
        //        $Rx_hex = '0'.$Rx_hex;
        //    }

        //    while (strlen($Ry_hex) < 64) {
        //        $Ry_hex = '0'.$Ry_hex;
        //    }

        //    // r = x1 mod n
        //    $r = gmp_strval(gmp_mod('0x'.$Rx_hex, $n_hex));

        //    // s = k^-1 * (e+d*r) mod n
        //    $edr = gmp_add($e, gmp_mul($d, $r));
        //    $invk = gmp_invert($k_hex, $n_hex);
        //    $kedr = gmp_mul($invk, $edr);
        //    $s = gmp_strval(gmp_mod($kedr, $n_hex));

        //    // The signature is the pair (r,s)
        //    $signature = array(
        //        'r' => Util::encodeHex($r),
        //        's' => Util::encodeHex($s),
        //    );

        //    while (strlen($signature['r']) < 64) {
        //        $signature['r'] = '0'.$signature['r'];
        //    }

        //    while (strlen($signature['s']) < 64) {
        //        $signature['s'] = '0'.$signature['s'];
        //    }
        //} while (gmp_cmp($r, '0') <= 0 || gmp_cmp($s, '0') <= 0);

        //$sig = array(
        //    'sig_rs' => $signature,
        //    'sig_hex' => self::serializeSig($signature['r'], $signature['s']),
        //);

        //return $sig['sig_hex']['seq'];
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
        $byte = '';
        $seq = '';
        $dec = '';
        $retval = array();
        $digits = array();

        for ($x = 0; $x < 256; $x++) {
            $digits[$x] = chr($x);
        }

        $dec = Util::decodeHex($r);

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

    /**
     * Verifies a previously generated ECDSA signature
     *
     * @param string
     * @param string
     * @param array
     * @param array
     * @param string
     * @return boolean
     */
    public function verifySignature($contract, $publicKey, $signature, $Q, $data)
    {
        $Gx = '0x'.substr(Secp256k1::G, 0, 62);
        $Gy = '0x'.substr(Secp256k1::G, 64, 62);

        $r = '0x'.$signature['r'];
        $s = '0x'.$signature['s'];

        $n_hex = '0x'.Secp256k1::N;
        $a_hex = '0x'.Secp256k1::A;
        $p_hex = '0x'.Secp256k1::P;

        // check to see if r,s are in [1,n-1]
        if (gmp_cmp($r, 1) <= 0 && gmp_cmp($r, $n_hex) > 0) {
            throw new Exception('r is out of range!');
        }

        if (gmp_cmp($s, 1) <= 0 && gmp_cmp($s, $n_hex) > 0) {
            throw new Exception('s is out of range!');
        }

        // convert the hash of the hex message to decimal
        $e = Util::decodeHex(hash('sha256', $data));

        // calculate w = s^-1 (mod n)
        $w = gmp_invert($s, $n_hex);

        // calculate u1 = e*w (mod n)
        $u1 = gmp_mod(gmp_mul($e, $w), $n_hex);

        // calculate u2 = r*w (mod n)
        $u2 = gmp_mod(gmp_mul($r, $w), $n_hex);

        $P = array('x' => $Gx, 'y' => $Gy);

        $Qx = '0x'.substr($Q, 2, 64);
        $Qy = '0x'.substr($Q, 66, 64);

        $Q = array('x' => $Qx, 'y' => $Qy);

        // Get new point Z(x1,y1) = (u1 * G) + (u2 * Q)
        $Za = Gmp::doubleAndAdd($u1, $P, $p_hex, $a_hex);
        $Zb = Gmp::doubleAndAdd($u2, $Q, $p_hex, $a_hex);
        $Z  = Gmp::gmpPointAdd($Za, $Zb, $p_hex, $a_hex);

        $Zx_hex = Util::encodeHex($Z['x']);
        $Zy_hex = Util::encodeHex($Z['y']);

        while (strlen($Zx_hex) < 64) {
            $Zx_hex = '0'.$Zx_hex;
        }

        while (strlen($Zy_hex) < 64) {
            $Zy_hex = '0'.$Zy_hex;
        }

        // Signature is valid if r is congruent to x1 (mod n)
        // or in other words, if r - x1 is an integer multiple of n
        $rsubx = gmp_sub($r, '0x'.$Zx_hex);
        $rsubx_rem = gmp_div_r($rsubx, $n_hex);

        if (gmp_cmp($rsubx_rem, '0') == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determines if a SIN is valid or not, depending on
     * rules defined in spec, see:
     * https://en.bitcoin.it/wiki/Identity_protocol_v1
     *
     * @param string
     * @return boolean
     */
    public function validateSin($sin)
    {
        return (!empty($sin) && (substr($sin, 0, 1) == 'T'));
    }
}
