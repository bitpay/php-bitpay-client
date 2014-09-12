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

namespace Bitpay\Util;

/**
 *
 * @package Bitcore
 */
class Gmp
{

    /**
     * @param string
     * @param array
     * @param string
     * @param string
     *
     * @return array
     */
    public static function doubleAndAdd($x, $P, $p, $a)
    {
        $tmp = self::gmpD2B($x);
        $n   = strlen($tmp) - 1;
        $S   = 'infinity';
        $D   = 0;
        $A   = 0;

        while ($n >= 0) {
            $D++;
            $S = self::gmpPointDouble($S, $p, $a);
            if ($tmp[$n] == 1) {
                $A++;
                $S = self::gmpPointAdd($S, $P, $p, $a);
            }
            $n--;
        }

        return $S;
    }

    /**
     * @param string
     *
     * @return string
     */
    public static function gmpD2B($num)
    {
        if (substr(strtolower($num), 0, 2) == '0x') {
            $num = Util::decodeHex(substr($num, 2));
        }
        $tmp  = $num;
        $iter = 0;
        $bin  = '';
        while (gmp_cmp($tmp, 0) > 0) {
            if (gmp_mod($tmp, 2) == 1) {
                $bin .= 1;
            } else {
                $bin .= 0;
            }
            $tmp = gmp_div($tmp, 2);
            $iter++;
        }

        return $bin;
    }

    /**
     * 2P = R where
     * s = (3xP2 + a)/(2yP) mod p
     * xR = s2 - 2xP mod p
     * yR = -yP + s(xP - xR) mod p
     *
     * @param array
     * @param string
     * @param string
     *
     * @return array
     */
    public static function gmpPointDouble($P, $p, $a)
    {
        if ($P == 'infinity') {
            return $P;
        }
        $s = 0;
        $R = array(
            'x' => 0,
            'y' => 0,
            's' => 0,
            'p' => $p,
            'a' => $a,
        );
        $m      = gmp_add(gmp_mul(3, gmp_mul($P['x'], $P['x'])), $a);
        $o      = gmp_mul(2, $P['y']);
        $n      = gmp_invert($o, $p);
        $n2     = gmp_mod($o, $p);
        $st     = gmp_mul($m, $n);
        $st2    = gmp_mul($m, $n2);
        $s      = gmp_mod($st, $p);
        $s2     = gmp_mod($st2, $p);
        $xmul   = gmp_mul(2, $P['x']);
        $smul   = gmp_mul($s, $s);
        $xsub   = gmp_sub($smul, $xmul);
        $xmod   = gmp_mod($xsub, $p);
        $R['x'] = $xmod;
        $ysub   = gmp_sub($P['x'], $R['x']);
        $ymul   = gmp_mul($s, $ysub);
        $ysub2  = gmp_sub(0, $P['y']);
        $yadd   = gmp_add($ysub2, $ymul);
        $R['x'] = gmp_strval($R['x']);
        $R['y'] = gmp_strval(gmp_mod($yadd, $p));
        $R['s'] = gmp_strval($s);

        return $R;
    }

    /**
     * P + Q = R where
     * s = (yP - yQ)/(xP - xQ) mod p
     * xR = s2 - xP - xQ mod p
     * yR = -yP + s(xP - xR) mod p
     *
     * @param array
     * @param array
     * @param string
     * @param string
     *
     * @return array
     */
    public static function gmpPointAdd($P, $Q, $p, $a)
    {
        if ($P == 'infinity') {
            return $Q;
        }

        if ($Q == 'infinity') {
            return $P;
        }

        if ($P == $Q) {
            return self::gmpPointDouble($P, $p, $a);
        }

        $s = 0;
        $R = array(
            'x' => 0,
            'y' => 0,
            's' => 0,
        );

        $m      = gmp_sub($P['y'], $Q['y']);
        $n      = gmp_sub($P['x'], $Q['x']);
        $o      = gmp_invert($n, $p);
        $st     = gmp_mul($m, $o);
        $s      = gmp_mod($st, $p);
        $R['x'] = gmp_mod(gmp_sub(gmp_sub(gmp_mul($s, $s), $P['x']), $Q['x']), $p);
        $R['y'] = gmp_mod(gmp_add(gmp_sub(0, $P['y']), gmp_mul($s, gmp_sub($P['x'], $R['x']))), $p);
        $R['s'] = gmp_strval($s);
        $R['x'] = gmp_strval($R['x']);
        $R['y'] = gmp_strval($R['y']);

        return $R;
    }

    /**
     * @param string
     *
     * @return string
     */
    public static function gmpBinconv($hex)
    {
        for ($x = 0; $x < 256; $x++) {
            $digits[$x] = chr($x);
        }
        $dec  = $hex;
        $byte = '';
        $seq  = '';

        if (substr(strtoupper($dec), 0, 2) != '0X') {
            $dec='0x'.strtoupper($dec);
        }

        while (gmp_cmp($dec, '0') > 0) {
            $dv   = gmp_div($dec, '256');
            $rem  = gmp_strval(gmp_mod($dec, '256'));
            $dec  = $dv;
            $byte = $byte.$digits[$rem];
        }

        return strrev($byte);
    }
}
