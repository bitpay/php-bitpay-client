<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

use Bitpay\PointInterface;
use Bitpay\Point;

/**
 * Provides methods used when creating elliptic curve keypairs
 * and related utility functions to support algorithms.
 *
 * @package Bitcore
 */
class Gmp
{
    /**
     * Pure PHP implementation of the doubleAndAdd algorithm, see:
     * http://en.wikipedia.org/wiki/Elliptic_curve_point_multiplication#Double-and-add
     *
     * @param string                  $hex
     * @param PointInterface          $point      Point to double
     * @param CurveParameterInterface $parameters Curve parameters
     *
     * @return PointInterface
     */
    public static function doubleAndAdd($hex, PointInterface $point, CurveParameterInterface $parameters = null)
    {
        if (null === $parameters) {
            $parameters = new Secp256k1();
        }

        $tmp = self::gmpD2B($hex);
        $n   = strlen($tmp) - 1;
        $S   = new Point(PointInterface::INFINITY, PointInterface::INFINITY);

        while ($n >= 0) {
            $S = self::gmpPointDouble($S);
            if ($tmp[$n] == 1) {
                $S = self::gmpPointAdd($S, $point);
            }
            $n--;
        }

        return new Point($S->getX(), $S->getY());
    }

    /**
     * This method returns a binary string representation of
     * the decimal number. Used for the doubleAndAdd() method.
     *
     * @see http://php.net/manual/en/function.decbin.php but for large numbers
     *
     * @param string
     * @return string
     */
    public static function gmpD2B($dec)
    {
        if (substr(strtolower($dec), 0, 2) == '0x') {
            $dec = Util::decodeHex(substr($dec, 2));
        }

        $bin  = '';
        while (gmp_cmp($dec, '0') > 0) {
            if (gmp_mod($dec, 2) == '1') {
                $bin .= '1';
            } else {
                $bin .= '0';
            }
            $dec = gmp_div($dec, 2);
        }

        return $bin;
    }

    /**
     * Point multiplication method 2P = R where
     *   s = (3xP2 + a)/(2yP) mod p
     *   xR = s2 - 2xP mod p
     *   yR = -yP + s(xP - xR) mod p
     *
     * @param PointInterface $point
     * @param CurveParameterInterface
     * @return PointInterface
     */
    public static function gmpPointDouble(PointInterface $point, CurveParameterInterface $parameters = null)
    {
        if ($point->isInfinity()) {
            return $point;
        }

        if (null === $parameters) {
            $parameters = new Secp256k1();
        }

        $p = $parameters->pHex();
        $a = $parameters->aHex();

        $s = 0;
        $R = array(
            'x' => 0,
            'y' => 0,
        );

        // Critical math section
        try {
            $m      = gmp_add(gmp_mul(3, gmp_mul($point->getX(), $point->getX())), $a);
            $o      = gmp_mul(2, $point->getY());
            $n      = gmp_invert($o, $p);
            $n2     = gmp_mod($o, $p);
            $st     = gmp_mul($m, $n);
            $st2    = gmp_mul($m, $n2);
            $s      = gmp_mod($st, $p);
            //$s2     = gmp_mod($st2, $p);
            $xmul   = gmp_mul(2, $point->getX());
            $smul   = gmp_mul($s, $s);
            $xsub   = gmp_sub($smul, $xmul);
            $xmod   = gmp_mod($xsub, $p);
            $R['x'] = $xmod;
            $ysub   = gmp_sub($point->getX(), $R['x']);
            $ymul   = gmp_mul($s, $ysub);
            $ysub2  = gmp_sub(0, $point->getY());
            $yadd   = gmp_add($ysub2, $ymul);
            $R['x'] = gmp_strval($R['x']);
            $R['y'] = gmp_strval(gmp_mod($yadd, $p));
        } catch (\Exception $e) {
            throw new \Exception('Error in Util::gmpPointDouble(): '.$e->getMessage());
        }

        return new Point($R['x'], $R['y']);
    }

    /**
     * Point addition method P + Q = R where:
     *   s = (yP - yQ)/(xP - xQ) mod p
     *   xR = s2 - xP - xQ mod p
     *   yR = -yP + s(xP - xR) mod p
     *
     * @param PointInterface
     * @param PointInterface
     *
     * @return PointInterface
     */
    public static function gmpPointAdd(PointInterface $P, PointInterface $Q)
    {
        if ($P->isInfinity()) {
            return $Q;
        }

        if ($Q->isInfinity()) {
            return $P;
        }

        if ($P->getX() == $Q->getX() && $P->getY() == $Q->getY()) {
            return self::gmpPointDouble(new Point($P->getX(), $P->getY()));
        }

        $p = '0x'.Secp256k1::P;
        //$a = '0x'.Secp256k1::A;
        $s = 0;
        $R = array(
            'x' => 0,
            'y' => 0,
            's' => 0,
        );

        // Critical math section
        try {
            $m      = gmp_sub($P->getY(), $Q->getY());
            $n      = gmp_sub($P->getX(), $Q->getX());
            $o      = gmp_invert($n, $p);
            $st     = gmp_mul($m, $o);
            $s      = gmp_mod($st, $p);

            $R['x'] = gmp_mod(
                gmp_sub(
                    gmp_sub(
                        gmp_mul($s, $s),
                        $P->getX()
                    ),
                    $Q->getX()
                ),
                $p
            );
            $R['y'] = gmp_mod(
                gmp_add(
                    gmp_sub(
                        0,
                        $P->getY()
                    ),
                    gmp_mul(
                        $s,
                        gmp_sub(
                            $P->getX(),
                            $R['x']
                        )
                    )
                ),
                $p
            );

            $R['s'] = gmp_strval($s);
            $R['x'] = gmp_strval($R['x']);
            $R['y'] = gmp_strval($R['y']);
        } catch (\Exception $e) {
            throw new \Exception('Error in Util::gmpPointAdd(): '.$e->getMessage());
        }

        return new Point($R['x'], $R['y']);
    }

    /**
     * Converts hex value into octet (byte) string
     *
     * @param string
     *
     * @return string
     */
    public static function gmpBinconv($hex)
    {
        $rem    = '';
        $dv     = '';
        $byte   = '';
        $digits = array();

        for ($x = 0; $x < 256; $x++) {
            $digits[$x] = chr($x);
        }

        if (substr(strtolower($hex), 0, 2) != '0x') {
            $hex = '0x'.strtolower($hex);
        }

        while (gmp_cmp($hex, 0) > 0) {
            $dv   = gmp_div($hex, 256);
            $rem  = gmp_strval(gmp_mod($hex, 256));
            $hex  = $dv;
            $byte = $byte.$digits[$rem];
        }

        return strrev($byte);
    }

    /**
     * y^2 (mod p) = x^3 + ax + b (mod p)
     *
     * @param PointInterface $point
     * @param CurveParameterInterface $parameters
     */
    public static function pointTest(PointInterface $point, CurveParameterInterface $parameters = null)
    {
        if (null === $parameters) {
            $parameters = new Secp256k1();
        }

        // y^2
        $y2 = gmp_pow($point->getY(), 2);
        // x^3
        $x3 = gmp_pow($point->getX(), 3);
        // ax
        $ax = gmp_mul($parameters->aHex(), $point->getX());

        $left  = gmp_strval(gmp_mod($y2, $parameters->pHex()));
        $right = gmp_strval(
            gmp_mod(
                gmp_add(
                    gmp_add($x3, $ax),
                    $parameters->bHex()
                ),
                $parameters->pHex()
            )
        );

        return ($left == $right);
    }
}
