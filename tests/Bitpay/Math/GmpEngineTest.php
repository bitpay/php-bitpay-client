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
        $this->assertEquals(gmp_strval(gmp_add($a, $a)), $math->add($a, $a));
        $this->assertEquals(gmp_strval(gmp_add($b, $b)), $math->add($b, $b));
        $this->assertEquals(gmp_strval(gmp_add($c, $c)), $math->add($c, $c));
        $this->assertEquals(2, $math->add(1, 1));
    }

    public function testcmp()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals(gmp_strval(gmp_cmp($a, $a)), $math->cmp($a, $a));
        $this->assertEquals(gmp_strval(gmp_cmp($b, $b)), $math->cmp($b, $b));
        $this->assertEquals(gmp_strval(gmp_cmp($c, $c)), $math->cmp($c, $c));
        $this->assertEquals(0, $math->cmp(1, 1));
    }

    public function testdiv()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals(gmp_strval(gmp_div($a, $a)), $math->div($a, $a));
        $this->assertEquals(gmp_strval(gmp_div($b, $b)), $math->div($b, $b));
        $this->assertEquals(gmp_strval(gmp_div($c, $c)), $math->div($c, $c));
        $this->assertEquals(1, $math->div(1, 1));
    }

    public function testinvertm()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals(gmp_strval(gmp_invert($a, $a)), $math->invertm($a, $a));
        $this->assertEquals(gmp_strval(gmp_invert($b, $b)), $math->invertm($b, $b));
        $this->assertEquals(gmp_strval(gmp_invert($c, $c)), $math->invertm($c, $c));
        $this->assertEquals(0, $math->invertm(1, 1));

        $o = '2';
        $p = '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffefffffc2f';
        $this->assertEquals('57896044618658097711785492504343953926634992332820282019728792003954417335832', $math->invertm($o, $p));

        $o = '-207267379875244730201206352791949018434229233557197871725317424106240926035466';
        $p = '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffefffffc2f';
        $this->assertEquals('93736451599995461267424215486556527005103980679329099329644578865571485201981', $math->invertm($o, $p));
    }

    public function testmod()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals(gmp_strval(gmp_mod($a, $a)), $math->mod($a, $a));
        $this->assertEquals(gmp_strval(gmp_mod($b, $b)), $math->mod($b, $b));
        $this->assertEquals(gmp_strval(gmp_mod($c, $c)), $math->mod($c, $c));
        $this->assertEquals(0, $math->mod(1, 1));
    }

    public function testmul()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals(gmp_strval(gmp_mul($a, $a)), $math->mul($a, $a));
        $this->assertEquals(gmp_strval(gmp_mul($b, $b)), $math->mul($b, $b));
        $this->assertEquals(gmp_strval(gmp_mul($c, $c)), $math->mul($c, $c));
        $this->assertEquals(1, $math->mul(1, 1));
    }


    public function testsub()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new GmpEngine();
        $this->assertEquals(gmp_strval(gmp_sub($a, $a)), $math->sub($a, $a));
        $this->assertEquals(gmp_strval(gmp_sub($b, $b)), $math->sub($b, $b));
        $this->assertEquals(gmp_strval(gmp_sub($c, $c)), $math->sub($c, $c));
        $this->assertEquals(0, $math->sub(1, 1));
    }
}
