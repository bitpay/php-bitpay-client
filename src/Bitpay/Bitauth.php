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

use Bitpay\Util\Util;
use Bitpay\Util\Gmp;
use Bitpay\Util\Base58;
use Bitpay\Util\Secp256k1;

/**
 * This provides an easy interface for implementing Bitauth
 *
 * @package Bitauth
 */
class Bitauth
{

    /**
     * Initialization Vector
     */
    const IV = '0000000000000000';

    /**
     */
    public function __construct()
    {
        if (!function_exists('openssl_open')) {
            // TODO: Throw exception
            die('FATAL Error in Bitauth::construct(): Your OpenSSL implementation is either too old or broken. Please contact your server administrator.');
        }
        
        if (!function_exists('mcrypt_list_algorithms')) {
            // TODO: Throw exception
            die('FATAL Error in Bitauth::construct(): Missing mcrypt PHP extension. Cannot continue.');
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
        if (!ctype_xdigit($privateKey->getHex())) {
            die('FATAL error in Bitauth::sign(): The private key must be in hex format.');
        }

        if (empty($data)) {
            die('FATAL error in Bitauth::sign(): You did not provide any data to sign.');
        }

        $e = Util::decodeHex(hash('sha256', $data));

        do {
            if (substr(strtolower($privateKey->getHex()), 0, 2) != '0x') {
                $d = '0x' . $privateKey->getHex();
            } else {
                $d = $privateKey->getHex();
            }

            $k = openssl_random_pseudo_bytes(32, $cstrong);
            
            if (!$k || !$cstrong) {
                die('FATAL error in Bitauth::sign(): Could not generate a cryptographically-strong random number. Your OpenSSL extension might be old or broken.');
            }

            $k_hex = '0x' . strtolower(bin2hex($k));
            $n_hex = '0x' . Secp256k1::N;
            $a_hex = '0x' . Secp256k1::A;
            $p_hex = '0x' . Secp256k1::P;

            $Gx = '0x' . substr(Secp256k1::G, 0, 62);
            $Gy = '0x' . substr(Secp256k1::G, 64, 62);

            $P = array('x' => $Gx, 'y' => $Gy);

            // Calculate a new curve point from Q=k*G (x1,y1)
            $R = Gmp::doubleAndAdd($k_hex, $P, $p_hex, $a_hex);

            $Rx_hex = Util::encodeHex($R['x']);
            $Ry_hex = Util::encodeHex($R['y']);

            while (strlen($Rx_hex) < 64) {
                $Rx_hex = '0' . $Rx_hex;
            }

            while (strlen($Ry_hex) < 64) {
                $Ry_hex = '0' . $Ry_hex;
            }

            // r = x1 mod n
            $r = gmp_strval(gmp_mod('0x' . $Rx_hex, $n_hex));

            // s = k^-1 * (e+d*r) mod n
            $edr = gmp_add($e, gmp_mul($d, $r));
            $invk = gmp_invert($k_hex, $n_hex);
            $kedr = gmp_mul($invk, $edr);
            $s = gmp_strval(gmp_mod($kedr, $n_hex));

            // The signature is the pair (r,s)
            $signature = array(
                               'r' => Util::encodeHex($r),
                               's' => Util::encodeHex($s)
                              );

            while (strlen($signature['r']) < 64) {
                $signature['r'] = '0' . $signature['r'];
            }

            while (strlen($signature['s']) < 64) {
                $signature['s'] = '0' . $signature['s'];
            }

        } while (gmp_cmp($r, '0') <= 0 || gmp_cmp($s, '0') <= 0);

        $sig = array(
                     'sig_rs' => $signature,
                     'sig_hex' => self::serializeSig($signature['r'], $signature['s'])
                    );

        return $sig['sig_hex']['seq'];
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
            $byte = $byte . $digits[$rem];
        }

        $byte = strrev($byte);

        // msb check
        if (gmp_cmp('0x' . bin2hex($byte[0]), '0x80') >= 0) {
            $byte = chr(0x00) . $byte;
        }

        $retval['bin_r'] = bin2hex($byte);
        $seq = chr(0x02) . chr(strlen($byte)) . $byte;
        $dec = Util::decodeHex($s);

        $byte = '';

        while (gmp_cmp($dec, '0') > 0) {
            $dv = gmp_div($dec, '256');
            $rem = gmp_strval(gmp_mod($dec, '256'));
            $dec = $dv;
            $byte = $byte . $digits[$rem];
        }

        $byte = strrev($byte);

        // msb check
        if (gmp_cmp('0x' . bin2hex($byte[0]), '0x80') >= 0) {
            $byte = chr(0x00) . $byte;
        }

        $retval['bin_s'] = bin2hex($byte);
        $seq = $seq . chr(0x02) . chr(strlen($byte)) . $byte;
        $seq = chr(0x30) . chr(strlen($seq)) . $seq;
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
        $Gx = '0x' . substr(Secp256k1::G, 0, 62);
        $Gy = '0x' . substr(Secp256k1::G, 64, 62);

        $r = '0x' . $signature['r'];
        $s = '0x' . $signature['s'];

        $n_hex = '0x' . Secp256k1::N;
        $a_hex = '0x' . Secp256k1::A;
        $p_hex = '0x' . Secp256k1::P;

        // check to see if r,s are in [1,n-1]
        if (gmp_cmp($r, 1) <= 0 && gmp_cmp($r, $n_hex) > 0) {
            die('FATAL Error in Bitauth::verifySignature(): r is out of range!');
        }

        if (gmp_cmp($s, 1) <= 0 && gmp_cmp($s, $n_hex) > 0) {
            die('FATAL Error in Bitauth::verifySignature():s is out of range!');
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

        $Qx = '0x' . substr($Q, 2, 64);
        $Qy = '0x' . substr($Q, 66, 64);

        $Q = array('x' => $Qx, 'y' => $Qy);

        // Get new point Z(x1,y1) = (u1 * G) + (u2 * Q)
        $Za = Gmp::doubleAndAdd($u1, $P, $p_hex, $a_hex);
        $Zb = Gmp::doubleAndAdd($u2, $Q, $p_hex, $a_hex);
        $Z  = Gmp::gmpPointAdd($Za, $Zb, $p_hex, $a_hex);

        $Zx_hex = Util::encodeHex($Z['x']);
        $Zy_hex = Util::encodeHex($Z['y']);

        while (strlen($Zx_hex) < 64) {
            $Zx_hex = '0' . $Zx_hex;
        }

        while (strlen($Zy_hex) < 64) {
            $Zy_hex = '0' . $Zy_hex;
        }

        // Signature is valid if r is congruent to x1 (mod n)
        // or in other words, if r - x1 is an integer multiple of n
        $rsubx = gmp_sub($r, '0x' . $Zx_hex);
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
        // TODO
        return true;
    }

    /**
     * Returns the inititialization size used for a particular cypher
     * type.  Returns an integer IV size on success or boolean false
     * on failure.  If no IV is needed for the cypher type and mode,
     * a zero is returned.
     *
     * @param  string   $cypher_type
     * @return int|bool
     */
    public function GetIVSize($cypher_type = 'MCRYPT_TRIPLEDES')
    {

        $block_mode = 'cbc';

        return mcrypt_get_iv_size($cypher_type, $block_mode);

    }

    /**
     * Returns the maximum key size that can be used with a particular
     * cypher type. Any key size equal to or less than the returned
     * value are legal key sizes.  Depending on if the local mycrypt
     * extension is linked against 2.2 or 2.3/2.4 the block mode could
     * be required, hence the if/else statement.
     *
     * @param  string $cypher_type
     * @return int
     */
    public function GetKeySize($cypher_type = 'MCRYPT_TRIPLEDES')
    {

        $block_mode = 'cbc';

        $max_key_size = mcrypt_get_key_size($cipher_type);

        if ($max_key_size !== false) {
            return $max_key_size;
        } else {
            return mcrypt_get_key_size($cipher_type, $block_mode);
        }

    }

    /**
     * Returns a list of all supported mcrypt algorithms on the local system.
     *
     * @param none
     * @return array
     */
    public function GetAlgos()
    {
        return mcrypt_list_algorithms();
    }

    /**
     * Performs an internal self-test on the specified mcrypt algorithm and
     * returns either boolean true/false depending on if the self-test passed
     * or failed.
     *
     * @param  string  $cypher_type
     * @return boolean
     */
    public function AlgoSelfTest($cypher_type = 'MCRYPT_TRIPLEDES')
    {
        return mcrypt_module_self_test($cypher_type);
    }

    /**
     *
     * Encrypts $text based on your $key and $iv.  The returned text is
     * base-64 encoded to make it easier to work with in various scenarios.
     * Default cypher is MCRYPT_TRIPLEDES but you can substitute depending
     * on your specific encryption needs.
     *
     * @param  string    $text
     * @param  string    $key
     * @param  string    $iv
     * @param  int       $bit_check
     * @param  string    $cypher_type
     * @return string    $text
     * @throws Exception $e
     *
     */
    public function Encrypt($text, $key = '', $iv = '', $bit_check = 8, $cypher_type = 'MCRYPT_TRIPLEDES')
    {

        try {

            /* Ensure the key & IV is the same for both encrypt & decrypt. */
            if (!empty($text) && is_string($text)) {
                $text_num = str_split($text, $bit_check);
                $text_num = $bit_check - strlen($text_num[count($text_num) - 1]);

                for ($i = 0; $i < $text_num; $i++) {
                    $text = $text . chr($text_num);
                }

                $cipher = mcrypt_module_open($cypher_type, '', 'cbc', '');
                mcrypt_generic_init($cipher, $key, $iv);

                $encrypted = mcrypt_generic($cipher, $text);
                mcrypt_generic_deinit($cipher);

                mcrypt_module_close($cipher);

                return base64_encode($encrypted);
            } else {
                return $text;
            }

        } catch (Exception $e) {
            return 'Error in Bitauth::Encrypt(): ' . $e->getMessage();
        }

    }

    /**
     *
     * Decrypts $text based on your $key and $iv.  Make sure you use the same key
     * and initialization vector that you used when encrypting the $text. Default
     * cypher is MCRYPT_TRIPLEDES but you can substitute depending on the cypher
     * used for encrypting the text - very important.
     *
     * @param  string    $encrypted_text
     * @param  string    $key
     * @param  string    $iv
     * @param  int       $bit_check
     * @param  string    $cypher_type
     * @return string    $text
     * @throws Exception $e
     *
     */
    public function Decrypt($encrypted_text, $key = '', $iv = '', $bit_check = 8, $cypher_type = 'MCRYPT_TRIPLEDES')
    {

        try {

            /* Ensure the key & IV is the same for both encrypt & decrypt. */
            if (!empty($encrypted_text)) {
                $cipher = mcrypt_module_open($cypher_type, '', 'cbc', '');

                mcrypt_generic_init($cipher, $key, $iv);
                $decrypted = mdecrypt_generic($cipher, base64_decode($encrypted_text));

                mcrypt_generic_deinit($cipher);
                $last_char = substr($decrypted, -1);

                for ($i = 0; $i < $bit_check - 1; $i++) {
                    if (chr($i) == $last_char) {
                        $decrypted = substr($decrypted, 0, strlen($decrypted) - $i);
                        break;
                    }
                }

                mcrypt_module_close($cipher);

                return $decrypted;
            } else {
                return $encrypted_text;
            }

        } catch (Exception $e) {
            return 'Error in Bitauth::Dncrypt(): ' . $e->getMessage();
        }

    }

}
