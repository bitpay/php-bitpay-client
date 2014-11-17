<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class BcEngine implements EngineInterface
{
    const HEX_CHARS = '0123456789abcdef';
    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function add($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcadd($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function cmp($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bccomp($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function div($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcdiv($a, $b);
    }

    /**
     * @param String $a is number to be inverted
     * @param String $b is Modulus
     */
    public function invertm($a, $b)
    {
        $number = $this->input($a);
        $modulus = $this->input($b);
        if (!$this->coprime($number, $modulus)) {
            return '0';
        }
        $a = '1';
        $b = '0';
        $z = '0';
        $c = '0';
        $mod = $modulus;
        $num = $number;
        do {
            $z = bcmod($num, $mod);
            $c = bcdiv($num, $mod);
            $mod = $z;
            $z = bcsub($a, bcmul($b, $c));
            $num = $mod;
            $a = $b;
            $b = $z;
        } while (bccomp($mod, '0') > 0);
        if (bccomp($a, '0') < 0) {
            $a = bcadd($a, $modulus);
        }

        return (string) $a;
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function mod($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcmod($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function mul($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcmul($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function pow($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcpow($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function sub($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return bcsub($a, $b);
    }

    private function input($x)
    {
        if (is_string($x) && strtolower(substr($x, 0, 2)) == '0x') {
            $hex = strtolower($x);
            $hex = substr($hex, 2);

            for ($dec = '0', $i = 0; $i < strlen($hex); $i++) {
                $current = strpos('0123456789abcdef', $hex[$i]);
                $dec     = bcadd(bcmul($dec, 16), $current);
            }

            return $dec;
        }

        return $x;
    }

    /**
     * Function to determine if two numbers are
     * co-prime according to the Euclidean algo.
     *
     * @param  string $a First param to check.
     * @param  string $b Second param to check.
     * @return bool   Whether the params are cp.
     */
    public function coprime($a, $b)
    {
        $small = 0;
        $diff  = 0;
        while (bccomp($a, '0') > 0 && bccomp($b, '0') > 0) {
            if (bccomp($a, $b) == -1) {
                $small = $a;
                $diff  = bcmod($b, $a);
            }
            if (bccomp($a, $b) == 1) {
                $small = $b;
                $diff = bcmod($a, $b);
            }
            if (bccomp($a, $b) == 0) {
                $small = $a;
                $diff  = bcmod($b, $a);
            }
            $a = $small;
            $b = $diff;
        }
        if (bccomp($a, '1') == 0) {
            return true;
        }

        return false;
    }
}
