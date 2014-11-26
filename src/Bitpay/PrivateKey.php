<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

use Bitpay\Util\Secp256k1;
use Bitpay\Util\Util;
use Bitpay\Util\SecureRandom;
use Bitpay\Math\Math;

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
     * @var string
     */
    public $pemEncoded = '';

    /**
     * @var array
     */
    public $pemDecoded = array();

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
        if (!empty($this->hex)) {
            return $this;
        }

        do {
            $privateKey = \Bitpay\Util\SecureRandom::generateRandom(32);
            $this->hex  = strtolower(bin2hex($privateKey));
        } while (Math::cmp('0x'.$this->hex, '1') <= 0 || Math::cmp('0x'.$this->hex, '0x'.Secp256k1::N) >= 0);

        $this->dec = Util::decodeHex($this->hex);

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
        if (!ctype_xdigit($this->hex)) {
            throw new \Exception('The private key must be in hex format.');
        }

        if (empty($data)) {
            throw new \Exception('You did not provide any data to sign.');
        }

        $e = Util::decodeHex(hash('sha256', $data));

        do {
            if (substr(strtolower($this->hex), 0, 2) != '0x') {
                $d = '0x'.$this->hex;
            } else {
                $d = $this->hex;
            }

            $k = SecureRandom::generateRandom(32);

            $k_hex = '0x'.strtolower(bin2hex($k));
            $n_hex = '0x'.Secp256k1::N;


            $Gx = '0x'.substr(Secp256k1::G, 2, 64);
            $Gy = '0x'.substr(Secp256k1::G, 66, 64);

            $P = new Point($Gx, $Gy);

            // Calculate a new curve point from Q=k*G (x1,y1)
            $R = Util::doubleAndAdd($k_hex, $P);

            $Rx_hex = Util::encodeHex($R->getX());

            $Rx_hex = str_pad($Rx_hex, 64, '0', STR_PAD_LEFT);

            // r = x1 mod n
            $r = Math::mod('0x'.$Rx_hex, $n_hex);

            // s = k^-1 * (e+d*r) mod n
            $edr  = Math::add($e, Math::mul($d, $r));
            $invk = Math::invertm($k_hex, $n_hex);
            $kedr = Math::mul($invk, $edr);

            $s = Math::mod($kedr, $n_hex);

            // The signature is the pair (r,s)
            $signature = array(
                'r' => Util::encodeHex($r),
                's' => Util::encodeHex($s),
            );

            $signature['r'] = str_pad($signature['r'], 64, '0', STR_PAD_LEFT);
            $signature['s'] = str_pad($signature['s'], 64, '0', STR_PAD_LEFT);
        } while (Math::cmp($r, '0') <= 0 || Math::cmp($s, '0') <= 0);

        $sig = array(
            'sig_rs'  => $signature,
            'sig_hex' => self::serializeSig($signature['r'], $signature['s']),
        );

        return $sig['sig_hex']['seq'];
    }

    /**
     * Verifies an ECDSA signature previously generated.
     *
     * @param string $r   The signature r coordinate in hex.
     * @param string $s   The signature s coordinate in hex.
     * @param string $msg The message signed.
     * @param array  $Q   The base point.
     */
    public function verify($r, $s, $msg)
    {
        if (!ctype_xdigit($r) || !ctype_xdigit($s)) {
            throw new \Exception('The (r, s) parameters must be in hex format.');
        }
        if (empty($Q) || empty($msg)) {
            throw new \Exception('The point and message parameters are required.');
        }
        $e         = '';
        $w         = '';
        $u1        = '';
        $u2        = '';
        $Zx_hex    = '';
        $Zy_hex    = '';
        $rsubx     = '';
        $rsubx_rem = '';
        $Za        = array();
        $Zb        = array();
        $Z         = array();
        $r = $this->coordinateCheck(trim(strtolower($r)));
        $s = $this->coordinateCheck(trim(strtolower($s)));
        $this->rangeCheck($r);
        $this->rangeCheck($s);
        /* Convert the hash of the hex message to decimal */
        $e = Util::decodeHex(hash('sha256', $msg));
        /* Calculate w = s^-1 (mod n) */
        $w = Math::invertm($s, Secp256k1::N);
        /* Calculate u1 = e*w (mod n) */
        $u1 = Math::mod(Math::mul($e, $w), Secp256k1::N);
        /* Calculate u2 = r*w (mod n) */
        $u2 = Math::mod(Math::mul($r, $w), Secp256k1::N);
        /* Get new point Z(x1,y1) = (u1 * G) + (u2 * Q) */
        $Gx = '0x'.substr(Secp256k1::G, 2, 64);
        $Gy = '0x'.substr(Secp256k1::G, 66, 64);

        $P = new Point($Gx, $Gy);
        $Q = $P;
        $Za = Util::doubleAndAdd($u1, $P);
        $Zb = Util::doubleAndAdd($u2, $Q);
        $Z  = Util::PointAdd($Za, $Zb);
        $Zx_hex = $this->zeroPad(Util::encodeHex($Z['x']), 64);
        $Zy_hex = $this->zeroPad(Util::encodeHex($Z['y']), 64);
        /*
         * A signature is valid if r is congruent to x1 (mod n)
         * or in other words, if r - x1 is an integer multiple of n.
         */
        $rsubx     = Math::sub($r, '0x' . $Zx_hex);
        $rsubx_rem = Math::mod($rsubx, Secp256k1::N);
        return (math::cmp($rsubx_rem, '0') == 0);
    }

    /**
     * Basic coordinate check.
     *
     * @param  string $hex The coordinate to check.
     * @return string $hex The checked coordinate.
     */
    private function coordinateCheck($hex)
    {
        if ($this->testOx($hex) != $hex) {
            $hex = '0x' . $hex;
            if (strlen($hex) < 64) {
                throw new \Exception('The r parameter is invalid. Expected hex string of 64 characters (32-bytes).');
            }
        }
        return $hex;
    }

    /**
     * Determines if the hex value needs '0x'.
     *
     * @param  string $value The value to check.
     * @return string $value If the value is present.
     */
    public function testOx($value)
    {
        if (substr(strtolower($value), 0, 2) != '0x') {
            $value = '0x' . strtolower($value);
        }
        
        return $value;
    }

    /**
     * Consistent string padding workaround.
     * 
     * @param  string $value The value to pad.
     * @param  int    $amt   The amount to pad.
     * @return string $value The padded value.
     */
    public function zeroPad($value, $amt)
    {
        $val_len = strlen($value);
        while ($value < $amt) {
            $value = '0' . $value;
            $val_len++;
        }
        return $value;
    }

    /**
     * Basic range check. Throws exception if out of range.
     *
     * @param string $value The coordinate to check.
     */
    public function rangeCheck($value)
    {
        /* Check to see if $value is in the range [1, n-1] */
        if (Math::cmp($value, '1') <= 0 && Math::cmp($value, $this->n) > 0) {
            throw new \Exception('The parameter is out of range. Should be 1 < r < n-1.');
        }
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
        $dec  = '';
        $byte = '';
        $seq  = '';

        $digits = array();
        $retval = array();

        for ($x = 0; $x < 256; $x++) {
            $digits[$x] = chr($x);
        }

        $dec = Util::decodeHex($r);

        while (Math::cmp($dec, '0') > 0) {
            $dv = Math::div($dec, '256');
            $rem = Math::mod($dec, '256');
            $dec = $dv;
            $byte = $byte.$digits[$rem];
        }

        $byte = strrev($byte);

        // msb check
        if (Math::cmp('0x'.bin2hex($byte[0]), '0x80') >= 0) {
            $byte = chr(0x00).$byte;
        }

        $retval['bin_r'] = bin2hex($byte);
        $seq = chr(0x02).chr(strlen($byte)).$byte;
        $dec = Util::decodeHex($s);

        $byte = '';

        while (Math::cmp($dec, '0') > 0) {
            $dv = Math::div($dec, '256');
            $rem = Math::mod($dec, '256');
            $dec = $dv;
            $byte = $byte.$digits[$rem];
        }

        $byte = strrev($byte);

        // msb check
        if (Math::cmp('0x'.bin2hex($byte[0]), '0x80') >= 0) {
            $byte = chr(0x00).$byte;
        }

        $retval['bin_s'] = bin2hex($byte);
        $seq = $seq.chr(0x02).chr(strlen($byte)).$byte;
        $seq = chr(0x30).chr(strlen($seq)).$seq;
        $retval['seq'] = bin2hex($seq);

        return $retval;
    }

    /**
     * Decodes PEM data to retrieve the keypair.
     *
     * @param  string $pem_data The data to decode.
     * @return array            The keypair info.
     */
    public function pemDecode($pem_data)
    {
        $beg_ec_text = '-----BEGIN EC PRIVATE KEY-----';
        $end_ec_text = '-----END EC PRIVATE KEY-----';

        $decoded = '';

        $ecpemstruct = array();

        $pem_data = str_ireplace($beg_ec_text, '', $pem_data);
        $pem_data = str_ireplace($end_ec_text, '', $pem_data);
        $pem_data = str_ireplace("\r", '', trim($pem_data));
        $pem_data = str_ireplace("\n", '', trim($pem_data));
        $pem_data = str_ireplace(' ', '', trim($pem_data));

        $decoded = bin2hex(base64_decode($pem_data));

        if (strlen($decoded) < 230) {
            throw new \Exception('Invalid or corrupt secp256k1 key provided. Cannot decode the supplied PEM data.');
        }

        $ecpemstruct = array(
                'oct_sec_val'  => substr($decoded, 14, 64),
                'obj_id_val'   => substr($decoded, 86, 10),
                'bit_str_val'  => substr($decoded, 106),
        );

        if ($ecpemstruct['obj_id_val'] != '2b8104000a') {
            throw new \Exception('Invalid or corrupt secp256k1 key provided. Cannot decode the supplied PEM data.');
        }

        $private_key = $ecpemstruct['oct_sec_val'];
        $public_key  = $ecpemstruct['bit_str_val'];

        if (strlen($private_key) < 64 || strlen($public_key) < 128) {
            throw new \Exception('Invalid or corrupt secp256k1 key provided. Cannot decode the supplied PEM data.');
        }

        $this->pemDecoded = array('private_key' => $private_key, 'public_key' => $public_key);

        return $this->pemDecoded;
    }

    /**
     * Encodes keypair data to PEM format.
     *
     * @param  array  $keypair The keypair info.
     * @return string          The data to decode.
     */
    public function pemEncode($keypair)
    {
        if (is_array($keypair) && (strlen($keypair[0]) < 64 || strlen($keypair[1]) < 128)) {
            throw new \Exception('Invalid or corrupt secp256k1 keypair provided. Cannot decode the supplied PEM data.');
        }

        $dec         = '';
        $byte        = '';
        $beg_ec_text = '';
        $end_ec_text = '';
        $ecpemstruct = array();
        $digits      = array();

        for ($x = 0; $x < 256; $x++) {
            $digits[$x] = chr($x);
        }

        $ecpemstruct = array(
                'sequence_beg' => '30',
                'total_len'    => '74',
                'int_sec_beg'  => '02',
                'int_sec_len'  => '01',
                'int_sec_val'  => '01',
                'oct_sec_beg'  => '04',
                'oct_sec_len'  => '20',
                'oct_sec_val'  => $keypair[0],
                'a0_ele_beg'   => 'a0',
                'a0_ele_len'   => '07',
                'obj_id_beg'   => '06',
                'obj_id_len'   => '05',
                'obj_id_val'   => '2b8104000a',
                'a1_ele_beg'   => 'a1',
                'a1_ele_len'   => '44',
                'bit_str_beg'  => '03',
                'bit_str_len'  => '42',
                'bit_str_val'  => '00'.$keypair[1],
        );

        $beg_ec_text = '-----BEGIN EC PRIVATE KEY-----';
        $end_ec_text = '-----END EC PRIVATE KEY-----';

        $dec = trim(implode($ecpemstruct));

        if (strlen($dec) < 230) {
            throw new \Exception('Invalid or corrupt secp256k1 keypair provided. Cannot encode the supplied data.');
        }

        $dec = Util::decodeHex('0x'.$dec);

        while (Math::cmp($dec, '0') > 0) {
            $dv = Math::div($dec, '256');
            $rem = Math::mod($dec, '256');
            $dec = $dv;
            $byte = $byte.$digits[$rem];
        }

        $byte = $beg_ec_text."\r\n".chunk_split(base64_encode(strrev($byte)), 64).$end_ec_text;

        $this->pemEncoded = $byte;

        return $byte;
    }
}
