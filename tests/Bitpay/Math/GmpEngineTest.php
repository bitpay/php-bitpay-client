<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class GmpEngineTest extends \PHPUnit_Framework_TestCase
{
    private $a;
    private $b;
    private $c;
    private $math;

    /**
     * @requires  extension gmp
     */
    protected function setUp()
    {
        $this->a = 1234;
        $this->b = '1234123412341234123412341234123412412341234213412421341342342';
        $this->c = '0x1234123412341234123412341234123412412341234213412421341342342';

        if (!extension_loaded('gmp')) {
            $this->markTestSkipped('The GMP extension is NOT loaded! You must enable it to run this test');
        } else {
            $this->math = new GmpEngine();
        }
    }

    public function testAdd()
    {
        $this->assertEquals(gmp_strval(gmp_add($this->a, $this->a)), $this->math->add($this->a, $this->a));
        $this->assertEquals(gmp_strval(gmp_add($this->b, $this->b)), $this->math->add($this->b, $this->b));
        $this->assertEquals(gmp_strval(gmp_add($this->c, $this->c)), $this->math->add($this->c, $this->c));
        $this->assertEquals(2, $this->math->add(1, 1));
    }

    public function testCmp()
    {
        $this->assertEquals(gmp_strval(gmp_cmp($this->a, $this->a)), $this->math->cmp($this->a, $this->a));
        $this->assertEquals(gmp_strval(gmp_cmp($this->b, $this->b)), $this->math->cmp($this->b, $this->b));
        $this->assertEquals(gmp_strval(gmp_cmp($this->c, $this->c)), $this->math->cmp($this->c, $this->c));
        $this->assertEquals(0, $this->math->cmp(1, 1));
    }

    public function testDiv()
    {
        $this->assertEquals(gmp_strval(gmp_div($this->a, $this->a)), $this->math->div($this->a, $this->a));
        $this->assertEquals(gmp_strval(gmp_div($this->b, $this->b)), $this->math->div($this->b, $this->b));
        $this->assertEquals(gmp_strval(gmp_div($this->c, $this->c)), $this->math->div($this->c, $this->c));
        $this->assertEquals(1, $this->math->div(1, 1));
    }

    public function testInvertm()
    {
        $this->assertEquals(gmp_strval(gmp_invert($this->a, $this->a)), $this->math->invertm($this->a, $this->a));
        $this->assertEquals(gmp_strval(gmp_invert($this->b, $this->b)), $this->math->invertm($this->b, $this->b));
        $this->assertEquals(gmp_strval(gmp_invert($this->c, $this->c)), $this->math->invertm($this->c, $this->c));
        $this->assertEquals(0, $this->math->invertm(1, 1));

        $o = '2';
        $p = '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffefffffc2f';
        $this->assertEquals('57896044618658097711785492504343953926634992332820282019728792003954417335832', $this->math->invertm($o, $p));

        $o = '-207267379875244730201206352791949018434229233557197871725317424106240926035466';
        $p = '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffefffffc2f';
        $this->assertEquals('93736451599995461267424215486556527005103980679329099329644578865571485201981', $this->math->invertm($o, $p));
    }

    public function testMod()
    {
        $this->assertEquals(gmp_strval(gmp_mod($this->a, $this->a)), $this->math->mod($this->a, $this->a));
        $this->assertEquals(gmp_strval(gmp_mod($this->b, $this->b)), $this->math->mod($this->b, $this->b));
        $this->assertEquals(gmp_strval(gmp_mod($this->c, $this->c)), $this->math->mod($this->c, $this->c));
        $this->assertEquals(0, $this->math->mod(1, 1));
    }

    public function testMul()
    {
        $this->assertEquals(gmp_strval(gmp_mul($this->a, $this->a)), $this->math->mul($this->a, $this->a));
        $this->assertEquals(gmp_strval(gmp_mul($this->b, $this->b)), $this->math->mul($this->b, $this->b));
        $this->assertEquals(gmp_strval(gmp_mul($this->c, $this->c)), $this->math->mul($this->c, $this->c));
        $this->assertEquals(1, $this->math->mul(1, 1));
    }

    public function testPow()
    {
        $this->assertEquals(gmp_strval(gmp_pow($this->a, $this->a)), $this->math->pow($this->a, $this->a));
        $this->assertEquals(gmp_strval(gmp_pow($this->b, $this->b)), $this->math->pow($this->b, $this->b));
        $this->assertEquals(gmp_strval(gmp_pow($this->c, $this->c)), $this->math->pow($this->c, $this->c));
        $this->assertEquals(1, $this->math->pow(1, 1));
    }

    public function testSub()
    {
        $this->assertEquals(gmp_strval(gmp_sub($this->a, $this->a)), $this->math->sub($this->a, $this->a));
        $this->assertEquals(gmp_strval(gmp_sub($this->b, $this->b)), $this->math->sub($this->b, $this->b));
        $this->assertEquals(gmp_strval(gmp_sub($this->c, $this->c)), $this->math->sub($this->c, $this->c));
        $this->assertEquals(0, $this->math->sub(1, 1));
    }
}
