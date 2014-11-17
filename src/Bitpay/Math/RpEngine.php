<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class RpEngine implements EngineInterface
{
    const HEX_CHARS = '0123456789abcdef';
    public $rp;

    /* public constructor method to initialize important class properties */
    public function __construct()
    {
        $this->math = new RichArbitraryPrecisionIntegerMath();
    }
    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function add($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return $this->math->rpadd($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function cmp($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return $this->math->rpcomp($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function div($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return $this->math->rpdiv($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
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
            $z = $this->math->rpmod($num, $mod);
            $c = $this->math->rpdiv($num, $mod);
            $mod = $z;
            $z = $this->math->rpsub($a, $this->math->rpmul($b, $c));
            $num = $mod;
            $a = $b;
            $b = $z;
        } while ($this->math->rpcomp($mod, '0') > 0);
        if ($this->math->rpcomp($a, '0') < 0) {
            $a = $this->math->rpadd($a, $modulus);
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

        return $this->math->rpmod($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function mul($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return $this->math->rpmul($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function pow($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return $this->math->rppow($a, $b);
    }

    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public function sub($a, $b)
    {
        $a = $this->input($a);
        $b = $this->input($b);

        return $this->math->rpsub($a, $b);
    }

    private function input($x)
    {
        if (is_string($x) && strtolower(substr($x, 0, 2)) == '0x') {
            $hex = strtolower($x);
            $hex = substr($hex, 2);

            for ($dec = '0', $i = 0; $i < strlen($hex); $i++) {
                $current = strpos('0123456789abcdef', $hex[$i]);
                $dec     = $this->math->rpadd($this->math->rpmul($dec, 16), $current);
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
        while ($this->math->rpcomp($a, '0') > 0 && $this->math->rpcomp($b, '0') > 0) {
            if ($this->math->rpcomp($a, $b) == -1) {
                $small = $a;
                $diff  = $this->math->rpmod($b, $a);
            }
            if ($this->math->rpcomp($a, $b) == 1) {
                $small = $b;
                $diff = $this->math->rpmod($a, $b);
            }
            if ($this->math->rpcomp($a, $b) == 0) {
                $small = $a;
                $diff  = $this->math->rpmod($b, $a);
            }
            $a = $small;
            $b = $diff;
        }
        if ($this->math->rpcomp($a, '1') == 0) {
            return true;
        }

        return false;
    }
}
