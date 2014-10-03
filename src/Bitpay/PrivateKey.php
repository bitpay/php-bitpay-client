<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
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

            $Gx = '0x'.substr(Secp256k1::G, 2, 64);
            $Gy = '0x'.substr(Secp256k1::G, 66, 64);

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
        $dec = '';
        $byte = '';
        $seq = '';
        $digits = array();
        $retval = array();

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
        $seq         = '';
        $decoded     = '';
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

        while (gmp_cmp($dec, '0') > 0) {
            $dv = gmp_div($dec, '256');
            $rem = gmp_strval(gmp_mod($dec, '256'));
            $dec = $dv;
            $byte = $byte.$digits[$rem];
        }

        $byte = $beg_ec_text . "\r\n" . chunk_split(base64_encode(strrev($byte)), 64) . $end_ec_text;

        $this->pemEncoded = $byte;

        return $byte;
    }
}
