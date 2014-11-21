<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class RichArbitraryPrecisionIntegerMath
{
    const HEX_CHARS = '0123456789abcdef';
    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */

    private $internal      = false;
    private $a_orig_size   = 0;
    private $b_orig_size   = 0;
    private $a_padded_size = 0;
    private $b_padded_size = 0;
    private $subtotal      = '0';
    private $a_orig        = '';
    private $b_orig        = '';
    private $a_now         = '';
    private $b_now         = '';
    private $init_called   = false;
    private $math_type     = '';
    private $test          = false;
    private $maxint        = 0;
    private $digits        = array();

    /* public constructor method to initialize important class properties */
    public function __construct()
    {
        for ($x = 0; $x < 256; $x++) {
            $this->digits[$x] = chr($x);
        }
        if (PHP_INT_SIZE > 4) {
            $this->maxint = 10;
        } else {
            $this->maxint = 5;
        }
    }

    final public function rpmod($a, $b)
    {
        try {
            settype($a, 'string');
            settype($b, 'string');
            if (trim($b) == '' || empty($b)) {
                return 'undefined';
            }
            if (trim($a) == '' || empty($a)) {
                //return array('quotient' => '0', 'remainder' => '0');
                return '0';
            }
            $len_a = strlen($a);
            $len_b = strlen($b);
            if ($len_a < $this->maxint && $len_b < $this->maxint) {
                //return array('quotient' => (int)((int)$a / (int)$b), 'remainder' => (int)((int)$a % (int)$b));
                return (int) ((int) $a % (int) $b);
            }
            $c = 0;
            $s = 0;
            $i = 0;
            $rem = '';
            $result = '';
            $scale  = $len_a - $len_b;
            $larger = $this->rpcomp($a, $b);
            switch ($larger) {
                case 1:
                    $q   = $len_a - 1;
                    $r   = $len_b - 1;
                    $quo = $a;
                    $div = $b;
                    break;
                case 0:
                    //return array('quotient' => '1', 'remainder' => '0');
                    return '0';
                case -1:
                    //return array('quotient' => '0', 'remainder' => $a);
                    return $a;
                default:
                    return false;
            }
            $c_temp = 0;
            $s_temp = 0;
            $number_string = '';
            $result_r      = '';
            $quotient      = '';
            $passes = array();
            $rem = $quo;
            $qq  = 0;
            $mainbreak  = false;
            $chunk_size = $len_b;
            $c_temp = substr($quo, 0, $chunk_size);
            $position = strlen($c_temp) - 1;
            while (!$mainbreak >= 0 && $qq < 10) {
                $i = 0;
                $break = false;
                while ($this->rpcomp($c_temp, $div) < 0) {
                    $quotient = $quotient.'0';
                    $i++;
                    $c_temp = $c_temp.substr($quo, $position + $i, 1);
                }
                $position = $this->rpadd($position, $i);
                $i = 0;
                $chunk_size = 1;
                while (!$break) {
                    $i++;
                    $s_temp = $this->rpmul($div, $i);
                    if ($this->rpcomp($s_temp, $c_temp) > 0) {
                        $i--;
                        break;
                    }
                    if ($this->rpcomp($s_temp, $c_temp) == 0) {
                        break;
                    }
                    if ($i > 9) {
                        break;
                    }
                }
                $quotient = $quotient.$i;
                $rem = $this->rpsub($c_temp, $this->rpmul($div, $i));
                if (isset($quo[$position + 1])) {
                    $c_temp = $rem.$quo[$position + 1];
                } else {
                    $mainbreak = true;
                    break;
                }
                $position = $this->rpadd($position, '1');
                $qq++;
            }
            if (trim($rem) == '') {
                $rem = '0';
            }
            $quotient_len = strlen($quotient);
            while (substr($quotient, 0, 1) === '0' && $quotient_len > 0) {
                $quotient = substr($quotient, 1);
                $quotient_len--;
            }
            //return array('quotient' => $quotient, 'remainder' => $rem);
            return $rem;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /* multiplies a number 'a' by a number 'b' */
    final public function rpmul($x, $y)
    {
        try {
            settype($x, 'string');
            settype($y, 'string');
            if ((trim($x) == '' || empty($x)) || (trim($y) == '' || empty($y))) {
                return '0';
            }
            if ($y == '1') {
                return $x;
            }
            if ($x == '1') {
                return $y;
            }
            $x_size = strlen($x);
            $y_size = strlen($y);
            $chunk = 0;
            if ($x_size > $y_size) {
                $chunk = $x_size;
            } else {
                $chunk = $y_size;
            }
            if ($chunk < $this->maxint) {
                return (int) ((int) $x * (int) $y);
            }
            $m  = (int) ((int) $chunk / 2);
            $x1 = substr($x, 0, -$m);
            $x2 = substr($x, -$m);
            $y1 = substr($y, 0, -$m);
            $y2 = substr($y, -$m);
            $a  = $this->rpmul($x2, $y2);
            $b  = $this->rpmul($x1, $y1);
            $c  = $this->rpmul($this->rpadd($x1, $x2), $this->rpadd($y1, $y2));
            $d  = $this->rpsub($this->rpsub($c, $a), $b);
            for ($qq = 0; $qq < $m; $qq++) {
                $d = $d.'0';
            }
            $e = $b;
            for ($qq = 0; $qq < ($m * 2); $qq++) {
                $e = $e.'0';
            }

            return $this->rpadd($a, $this->rpadd($d, $e));
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /* adds a number 'a' by a number 'b' */
    final public function rpadd($a, $b)
    {
        try {
            settype($a, 'string');
            settype($b, 'string');
            if ((trim($a) == '' || empty($a)) && !empty($b)) {
                return $b;
            }
            if ((trim($b) == '' || empty($b)) && !empty($a)) {
                return $a;
            }
            if ((trim($a) == '' || empty($a)) && (trim($b) == '' || empty($b))) {
                return '0';
            }
            $len_a = strlen($a);
            $len_b = strlen($b);
            if ($len_a < $this->maxint && $len_b < $this->maxint) {
                return ((int) $a + (int) $b);
            }
            if ($a[0] == '0') {
                while ($len_a > 0 && $a[0] == '0') {
                    $a = substr($a, 1);
                    $len_a--;
                }
            }
            if ($b[0] == '0') {
                while ($len_b > 0 && $b[0] == '0') {
                    $b = substr($b, 1);
                    $len_b--;
                }
            }
            if ($a[0] == '-' || $b[0] == '-') {
                return $this->rpsub($a, $b);
            }
            if ((trim($a) == '' || empty($a)) && !empty($b)) {
                return $b;
            }
            if ((trim($b) == '' || empty($b)) && !empty($a)) {
                return $a;
            }
            if ((trim($a) == '' || empty($a)) && (trim($b) == '' || empty($b))) {
                return '0';
            }
            while ($len_a > $len_b) {
                $b = '0'.$b;
                $len_b++;
            }
            while ($len_b > $len_a) {
                $a = '0'.$a;
                $len_a++;
            }
            $q = $len_a - 1;
            $c_temp = 0;
            $s_temp = 0;
            $result = $number_string = '';
            while ($q >= 0) {
                $s_temp = (int) $a[$q] + (int) $b[$q] + (int) $c_temp;
                if ($s_temp >= 10) {
                    $c_temp = 1;
                    $str_s_temp = (string) $s_temp;
                    $result = $str_s_temp[1];
                } else {
                    $c_temp = 0;
                    $result = $s_temp;
                }
                $q--;
                $number_string .= $result;
            }
            if ($q < 0 && $c_temp == 1) {
                $number_string .= '1';
            }
            $number_string = strrev($number_string);
            $number_string_len = strlen($number_string);
            while ($number_string[0] == '0' && $number_string_len > 0) {
                $number_string = substr($number_string, 1);
                $number_string_len--;
            }

            return $number_string;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /* subtracts a number 'a' by a number 'b' */
    final public function rpsub($a, $b)
    {
        try {
            settype($a, 'string');
            settype($b, 'string');
            $len_a = strlen($a);
            $len_b = strlen($b);
            if ($len_a < $this->maxint && $len_b < $this->maxint) {
                return ((int) $a - (int) $b);
            }
            $c = 0;
            $s = 0;
            $i = 0;
            $apad = 0;
            $bpad = 0;
            $result = '';
            $numerator   = '';
            $denominator = '';
            $sign = '';
            $sign_a = '';
            $sign_b = '';
            if ($a[0] == '-') {
                $sign_a = '-';
                $a = substr($a, 1);
                $len_a--;
            }
            if ($b[0] == '-') {
                $sign_b = '-';
                $b = substr($b, 1);
                $len_b--;
            }
            $larger = $this->rpcomp($a, $b);
            switch ($larger) {
                case 1:
                    $numerator   = $a;
                    $denominator = $b;
                    if ($sign_a == '' && $sign_b == '') {
                        $sign = '';
                    }
                    if ($sign_a == '' && $sign_b == '-') {
                        return $this->rpadd($a, $b);
                    }
                    if ($sign_a == '-' && $sign_b == '-') {
                        $sign = '-';
                    }
                    if ($sign_a == '-' && $sign_b == '') {
                        $sign = '-';
                    }
                    break;
                case 0:
                    $numerator   = $a;
                    $denominator = $b;
                    if ($sign_a == '' && $sign_b == '') {
                        return '0';
                    }
                    if ($sign_a == '' && $sign_b == '-') {
                        return $this->rpadd($a, $b);
                    }
                    if ($sign_a == '-' && $sign_b == '-') {
                        $sign = '-';
                    }
                    if ($sign_a == '-' && $sign_b == '') {
                        return '0';
                    }
                    break;
                case -1:
                    $numerator   = $b;
                    $denominator = $a;
                    if ($sign_a == '' && $sign_b == '') {
                        $sign = '-';
                    }
                    if ($sign_a == '' && $sign_b == '-') {
                        return $this->rpadd($a, $b);
                    }
                    if ($sign_a == '-' && $sign_b == '-') {
                        $sign = '';
                    }
                    if ($sign_a == '-' && $sign_b == '') {
                        $sign = '-';
                    }
                    break;
                default:
                    die('FATAL - unable to determine num/denom from comp() result!');
            }
            while (strlen($numerator) > strlen($denominator)) {
                $denominator = '0'.$denominator;
            }
            $q             = strlen($numerator) - 1;
            $c_temp        = 0;
            $number_string = '';
            $s_temp        = 0;
            while ($q >= 0) {
                $num_temp    = (int) substr($numerator, $q, 1);
                $denom_temp  = (int) substr($denominator, $q, 1);
                $borrow_temp = (int) $num_temp - (int) $c_temp;
                if ($borrow_temp > $denom_temp) {
                    $s_temp = (int) $borrow_temp - (int) $denom_temp;
                    $c_temp = 0;
                }
                if ($denom_temp > $borrow_temp) {
                    $s_temp = (10 + $borrow_temp) - $denom_temp;
                    $c_temp = 1;
                }
                if ($borrow_temp == $denom_temp) {
                    $s_temp = 0;
                    $c_temp = 0;
                }
                $q = $q - 1;
                $number_string = $number_string.$s_temp;
            }
            $result_a = strrev($number_string);
            $result_a_len = strlen($result_a);
            while (substr($result_a, 0, 1) === '0' && $result_a_len > 0) {
                $result_a = substr($result_a, 1);
                $result_a_len--;
            }

            return $sign.$result_a;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /* divides a number 'a' by a number 'b' */
    final public function rpdiv($a, $b)
    {
        try {
            settype($a, 'string');
            settype($b, 'string');
            if (trim($b) == '' || empty($b)) {
                return 'undefined';
            }
            if (trim($a) == '' || empty($a)) {
                //return array('quotient' => '0', 'remainder' => '0');
                return '0';
            }
            $len_a = strlen($a);
            $len_b = strlen($b);
            if ($len_a < $this->maxint && $len_b < $this->maxint) {
                //return array('quotient' => (int)((int)$a / (int)$b), 'remainder' => (int)((int)$a % (int)$b));
                return (int) ((int) $a / (int) $b);
            }
            $c = 0;
            $s = 0;
            $i = 0;
            $rem = '';
            $result = '';
            $scale  = $len_a - $len_b;
            $larger = $this->rpcomp($a, $b);
            switch ($larger) {
                case 1:
                    $q   = $len_a - 1;
                    $r   = $len_b - 1;
                    $quo = $a;
                    $div = $b;
                    break;
                case 0:
                    //return array('quotient' => '1', 'remainder' => '0');
                    return '1';
                case -1:
                    //return array('quotient' => '0', 'remainder' => $a);
                    return '0';
                default:
                    return false;
            }
            $c_temp = 0;
            $s_temp = 0;
            $number_string = '';
            $result_r      = '';
            $quotient      = '';
            $passes = array();
            $rem = $quo;
            $qq  = 0;
            $mainbreak  = false;
            $chunk_size = $len_b;
            $c_temp = substr($quo, 0, $chunk_size);
            $position = strlen($c_temp) - 1;
            while (!$mainbreak >= 0 && $qq < 10) {
                $i = 0;
                $break = false;
                while ($this->rpcomp($c_temp, $div) < 0) {
                    $quotient = $quotient.'0';
                    $i++;
                    $c_temp = $c_temp.substr($quo, $position + $i, 1);
                }
                $position = $this->rpadd($position, $i);
                $i = 0;
                $chunk_size = 1;
                while (!$break) {
                    $i++;
                    $s_temp = $this->rpmul($div, $i);
                    if ($this->rpcomp($s_temp, $c_temp) > 0) {
                        $i--;
                        break;
                    }
                    if ($this->rpcomp($s_temp, $c_temp) == 0) {
                        break;
                    }
                    if ($i > 9) {
                        break;
                    }
                }
                $quotient = $quotient.$i;
                $rem = $this->rpsub($c_temp, $this->rpmul($div, $i));
                if (isset($quo[$position + 1])) {
                    $c_temp = $rem.$quo[$position + 1];
                } else {
                    $mainbreak = true;
                    break;
                }
                $position = $this->rpadd($position, '1');
                $qq++;
            }
            if (trim($rem) == '') {
                $rem = '0';
            }
            $quotient_len = strlen($quotient);
            while (substr($quotient, 0, 1) === '0' && $quotient_len > 0) {
                $quotient = substr($quotient, 1);
                $quotient_len--;
            }
            //return array('quotient' => $quotient, 'remainder' => $rem);
            return $quotient;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /* raises a number 'a' to a power 'b' */
    final public function rppow($a, $b)
    {
        try {
            settype($a, 'string');
            settype($b, 'string');
            if (trim($b) == '' || empty($b)) {
                return '1';
            }
            $len_a = strlen($a);
            $len_b = strlen($b);
            if ($len_a < $this->maxint && $len_b < $this->maxint) {
                return pow((int) $a, (int) $b);
            }
            $i = 1;
            $q = 0;
            $result = $a;
            $number_string = '';
            while ($this->rpcomp($b, $i) > 0 && $q < 100) {
                $result = $this->rpmul($result, $a);
                $q++;
                $i++;
            }
            if ($q >= 100) {
                return 'overflow';
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /* compares two numbers and returns 1 if a>b, 0 if a=b, -1 if a<b */
    final public function rpcomp($a, $b)
    {
        try {
            settype($a, 'string');
            settype($b, 'string');
            if ((trim($a) == '' || empty($a)) && (trim($b) == '' || empty($b))) {
                return 0;
            }
            if (trim($a) == '' || empty($a)) {
                return -1;
            }
            if (trim($b) == '' || empty($b)) {
                return 1;
            }
            $i = 0;
            $a_size = strlen($a);
            $b_size = strlen($b);
            if ($a_size > $b_size) {
                return 1;
            }
            if ($b_size > $a_size) {
                return -1;
            }
            if ($a == $b) {
                return 0;
            }
            while ($i < $a_size) {
                if ((int) $a[$i] > (int) $b[$i]) {
                    return 1;
                }
                if ((int) $b[$i] > (int) $a[$i]) {
                    return -1;
                }
                $i++;
            }

            return 0;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
