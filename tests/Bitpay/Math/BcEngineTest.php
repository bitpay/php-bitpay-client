<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class BcEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @requires  extension gmp
     * @requires  extension bcmath
     */
    protected function setUp()
    {
      if (!extension_loaded('bcmath'))
      {
        $this->markTestSkipped('The Bcmath extension is NOT loaded! You must enable it to run this test');
      } elseif (!extension_loaded('gmp'))
      {
        $this->markTestSkipped('The GMPmath extension is NOT loaded! You must enable it to run this test');
      }

    }
    public function testAdd()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new BcEngine();
        $this->assertEquals(gmp_strval(gmp_add($a, $a)), $math->add($a, $a));
        $this->assertEquals(gmp_strval(gmp_add($b, $b)), $math->add($b, $b));
        $this->assertEquals(gmp_strval(gmp_add($c, $c)), $math->add($c, $c));
        $this->assertEquals(2, $math->add(1, 1));
    }

    public function testCmp()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new BcEngine();
        $this->assertEquals(gmp_strval(gmp_cmp($a, $a)), $math->cmp($a, $a));
        $this->assertEquals(gmp_strval(gmp_cmp($b, $b)), $math->cmp($b, $b));
        $this->assertEquals(gmp_strval(gmp_cmp($c, $c)), $math->cmp($c, $c));
        $this->assertEquals(0, $math->cmp(1, 1));
    }

    public function testDiv()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new BcEngine();
        $this->assertEquals(gmp_strval(gmp_div($a, $a)), $math->div($a, $a));
        $this->assertEquals(gmp_strval(gmp_div($b, $b)), $math->div($b, $b));
        $this->assertEquals(gmp_strval(gmp_div($c, $c)), $math->div($c, $c));
        $this->assertEquals(1, $math->div(1, 1));
    }

    public function testiInvertm()
    {
        $math = new BcEngine();
        
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        
        
        $this->assertEquals(gmp_strval(gmp_invert($a, $a)), $math->invertm($a, $a));
        $this->assertEquals(gmp_strval(gmp_invert($b, $b)), $math->invertm($b, $b));
        $this->assertEquals(gmp_strval(gmp_invert($c, $c)), $math->invertm($c, $c));

        $this->assertEquals(gmp_strval(gmp_invert(15, 14)), $math->invertm(15, 14));
    	$this->assertEquals(gmp_strval(gmp_invert(-1, 1)), $math->invertm(-1, 1));
        $this->assertEquals(0, $math->invertm(1, 1));

        $o = '2';
        $p = '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffefffffc2f';
        $this->assertEquals('57896044618658097711785492504343953926634992332820282019728792003954417335832', $math->invertm($o, $p));

        $o = '-207267379875244730201206352791949018434229233557197871725317424106240926035466';
        $p = '0xfffffffffffffffffffffffffffffffffffffffffffffffffffffffefffffc2f';
        $this->assertEquals('93736451599995461267424215486556527005103980679329099329644578865571485201981', $math->invertm($o, $p));
    }

    public function testMod()
    {
        $a = 1234;
        $b = '-1675975991242824637446753124775730765934920727574049172215445180465096172921808707643480960976619010162856846742450225672776411590632518780962349126898196';
        $c = '115792089237316195423570985008687907853269984665640564039457584007908834671663';
        $math = new BcEngine();
        $gmp = new GmpEngine();
        $this->assertEquals($gmp->mod($a, $a), $math->mod($a, $a));
        $this->assertEquals($gmp->mod($a, $b), $math->mod($a, $b));
        $this->assertEquals($gmp->mod($b, $c), $math->mod($b, $c));

    }

    public function testMul()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new BcEngine();
        $this->assertEquals(gmp_strval(gmp_mul($a, $a)), $math->mul($a, $a));
        $this->assertEquals(gmp_strval(gmp_mul($b, $b)), $math->mul($b, $b));
        $this->assertEquals(gmp_strval(gmp_mul($c, $c)), $math->mul($c, $c));
        $this->assertEquals(1, $math->mul(1, 1));
    }

    public function testPow()
    {
        $a = 1234;
        $b = '1234';
        $c = '0x4D2';
        $math = new BcEngine();
        $this->assertEquals(gmp_strval(gmp_pow($a, $a)), $math->pow($a, $a));
        $this->assertEquals(gmp_strval(gmp_pow($b, $b)), $math->pow($b, $b));
        $this->assertEquals(gmp_strval(gmp_pow($c, $c)), $math->pow($c, $c));
        $this->assertEquals(1, $math->pow(1, 1));
    }

    public function testSub()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new BcEngine();
        $this->assertEquals(gmp_strval(gmp_sub($a, $a)), $math->sub($a, $a));
        $this->assertEquals(gmp_strval(gmp_sub($b, $b)), $math->sub($b, $b));
        $this->assertEquals(gmp_strval(gmp_sub($c, $c)), $math->sub($c, $c));
        $this->assertEquals(0, $math->sub(1, 1));
    }

    public function testInput()
    {
        $inputs = array(
            1234,
            '1234123412341234123412341234123412412341234213412421341342342',
            '0x1234123412341234123412341234123412412341234213412',
            -1234,
            '-1234123412341234123412341234123412412341234213412421341342342',
            '-0x1234123412341234123412341234123412412341234213412',
            false,
            null,
            0,
            ''
        );

        $outputs = array(
            '1234',
            '1234123412341234123412341234123412412341234213412421341342342',
            '7141538191659890405914342860980599801397657411485029184530',
            '-1234',
            '-1234123412341234123412341234123412412341234213412421341342342',
            '-7141538191659890405914342860980599801397657411485029184530',
            '0',
            '0',
            '0',
            '0'
        );

        $math = new BcEngine();
        for($i = 0, $size = count($inputs); $i < $size; $i++) {
            $this->assertEquals($outputs[$i], $math->input($inputs[$i]));
        }
    }

    /**
     * @expectedException Exception
     */
    public function testInputNonNumeric()
    {
        $math = new BcEngine();
        $math->input("safasdf");
    }

    /**
     * @expectedException Exception
     */
    public function testInputFloat()
    {
        $math = new BcEngine();
        $math->input(1.3);
    }

    public function testCoprime()
    {
        $a = '14';
        $b = '21';
        $math = new BcEngine();
        $this->assertFalse($math->coprime($a, $b));

        $a = '1';
        $b = '-1';
        $math = new BcEngine();
        $this->assertTrue($math->coprime($a, $b));

        $a = '14';
        $b = '15';
        $math = new BcEngine();
        $this->assertTrue($math->coprime($a, $b));
    }

}
