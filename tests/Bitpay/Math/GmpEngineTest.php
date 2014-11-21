<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class GmpEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @requires  extension gmp
     */
    protected function setUp()
    {
      if (!extension_loaded('gmp'))
      {
        $this->markTestSkipped('The GMP extension is NOT loaded! You must enable it to run this test');
      }
    }

   	public function testadd()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';

        $math = new GmpEngine();
        $this->assertEquals($math->add($a, $a), gmp_strval(gmp_add($a, $a)));
        $this->assertEquals($math->add($b, $b), gmp_strval(gmp_add($b, $b)));
        $this->assertEquals($math->add($c, $c), gmp_strval(gmp_add($c, $c)));
        $this->assertEquals($math->add(1, 1), 2);
    }

    public function testcmp()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals($math->cmp($a, $a), gmp_strval(gmp_cmp($a, $a)));
        $this->assertEquals($math->cmp($b, $b), gmp_strval(gmp_cmp($b, $b)));
        $this->assertEquals($math->cmp($c, $c), gmp_strval(gmp_cmp($c, $c)));
        $this->assertEquals($math->cmp(1, 1), 0);
    }

    public function testdiv()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals($math->div($a, $a), gmp_strval(gmp_div($a, $a)));
        $this->assertEquals($math->div($b, $b), gmp_strval(gmp_div($b, $b)));
        $this->assertEquals($math->div($c, $c), gmp_strval(gmp_div($c, $c)));
        $this->assertEquals($math->div(1, 1), 1);
    }

    public function testinvertm()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals($math->invertm($a, $a), gmp_strval(gmp_invert($a, $a)));
        $this->assertEquals($math->invertm($b, $b), gmp_strval(gmp_invert($b, $b)));
        $this->assertEquals($math->invertm($c, $c), gmp_strval(gmp_invert($c, $c)));
        $this->assertEquals($math->invertm(1, 1), 0);
    }

    public function testmod()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals($math->mod($a, $a), gmp_strval(gmp_mod($a, $a)));
        $this->assertEquals($math->mod($b, $b), gmp_strval(gmp_mod($b, $b)));
        $this->assertEquals($math->mod($c, $c), gmp_strval(gmp_mod($c, $c)));
        $this->assertEquals($math->mod(1, 1), 0);
    }

    public function testmul()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals($math->mul($a, $a), gmp_strval(gmp_mul($a, $a)));
        $this->assertEquals($math->mul($b, $b), gmp_strval(gmp_mul($b, $b)));
        $this->assertEquals($math->mul($c, $c), gmp_strval(gmp_mul($c, $c)));
        $this->assertEquals($math->mul(1, 1), 1);
    }

    public function testpow()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals($math->pow($a, $a), gmp_strval(gmp_pow($a, $a)));
        $this->assertEquals($math->pow($b, $b), gmp_strval(gmp_pow($b, $b)));
        $this->assertEquals($math->pow($c, $c), gmp_strval(gmp_pow($c, $c)));
        $this->assertEquals($math->pow(1, 1), 1);
    }

    public function testsub()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals($math->sub($a, $a), gmp_strval(gmp_sub($a, $a)));
        $this->assertEquals($math->sub($b, $b), gmp_strval(gmp_sub($b, $b)));
        $this->assertEquals($math->sub($c, $c), gmp_strval(gmp_sub($c, $c)));
        $this->assertEquals($math->sub(1, 1), 0);
    }
}
