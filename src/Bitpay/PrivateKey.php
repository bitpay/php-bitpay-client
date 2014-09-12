<?php

namespace Bitpay;

use Bitpay\Util\Base58;
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
        return (string) $this->getHex();
    }

    /**
     * Generates a a private key
     *
     * @return \Bitpay\PrivateKey
     */
    public function generate()
    {
        do {
            $privateKey = openssl_random_pseudo_bytes(32, $cstrong);
            $this->hex  = sprintf('0x%s', strtoupper(bin2hex($privateKey)));
        } while (!$cstrong || gmp_cmp($this->hex, 1) <= 0 || gmp_cmp($this->hex, Secp256k1::N) >= 0);

        $this->dec = Util::decodeHex($this->hex);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return (!empty($this->hex) && !empty($this->dec));
    }

    /**
     * @return string
     */
    public function sign($message)
    {
        $e = Util::decodeHex($message);
        do {
            $d    = '0x' . $this->getHex();
            $k    = openssl_random_pseudo_bytes(32);
            $kHex = '0x' . bin2hex($k);
            $gX   = '0x' . substr(Secp256k1::G, 2, 64);
            $gY   = '0x' . substr(Secp256k1::G, 66, 64);
            $p    = array(
                'x' => $gX,
                'y' => $gY,
            );
            $r     = Gmp::doubleAndAdd($this->getHex(), $p, Secp256k1::P, Secp256k1::A);
            $rXHex = Util::encodeHex($r['x']);
            $rYHex = Util::encodeHex($r['y']);

            while (strlen($rXHex) < 64) {
                $rXHex = '0' . $rXHex;
            }
            while (strlen($rYHex) < 64) {
                $rYHex = '0' . $rYHex;
            }

            $r2   = gmp_strval(gmp_mod('0x' . $rXHex, Secp256k1::N));
            $edr  = gmp_add($e, gmp_mul($d, $r2));
            $invk = gmp_invert($kHex, Secp256k1::N);
            $kedr = gmp_mul($invk, $edr);
            $s    = gmp_strval(gmp_mod($kedr, Secp256k1::N));

            $signature = array(
                'r' => Util::encodeHex($r2),
                's' => Util::encodeHex($s),
            );

            while (strlen($signature['r']) < 64) {
                $signature['r'] = '0' . $signature['r'];
            }
            while (strlen($signature['s']) < 64) {
                $signature['s'] = '0' . $signature['s'];
            }
        } while (gmp_cmp($r2, '0') <= 0 || gmp_cmp($s, '0') <= 0);

        return $signature;
    }
}
