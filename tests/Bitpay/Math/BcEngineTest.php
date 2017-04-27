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
      }
    }

    public function testConstruct()
    {
        bcscale(15);
        $math = new BcEngine();
        $this->assertEquals('16', bcdiv('105', '6.55957'));
    }

    public function testAdd()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new BcEngine();
        $this->assertEquals('2468', $math->add($a, $a));
        $this->assertEquals('2468246824682468246824682468246824824682468426824842682684684',
            $math->add($b, $b));
        $this->assertEquals('4020328592351456034599241982311497811554079037632048678982517743814198916',
            $math->add($c, $c));
        $this->assertEquals(2, $math->add(1, 1));
    }

    public function testCmp()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new BcEngine();
        $this->assertEquals('0', $math->cmp($a, $a));
        $this->assertEquals('1', $math->cmp($b, $a));
        $this->assertEquals('-1', $math->cmp($a, $b));
        $this->assertEquals('0', $math->cmp($c, $c));
        $this->assertEquals(0, $math->cmp(1, 1));
    }

    public function testDiv()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $d = 256;
        $math = new BcEngine();
        $this->assertEquals('4', $math->div($a, $d));
        $this->assertEquals('4820794579457945794579457945794579735707946146142270864618', $math->div($b, $d));
        $this->assertEquals('1', $math->div($c, $c));
        $this->assertEquals('7852204281936437567576644496702144163191560620375095076137729968387107',
            $math->div($c, $d));

        $this->assertEquals(1, $math->div(1, 1));
    }

    public function testiInvertm()
    {
        $math = new BcEngine();
        
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        
        
        $this->assertEquals(0, $math->invertm($a, $a));
        $this->assertEquals(0, $math->invertm($b, $b));
        $this->assertEquals(0, $math->invertm($c, $c));

        $this->assertEquals('1', $math->invertm(15, 14));
    	$this->assertEquals(0, $math->invertm(-1, 1));
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
        $this->assertEquals(0, $math->mod($a, $a));
        $this->assertEquals('1234', $math->mod($a, $b));
        $this->assertEquals('14474011154664524427946373126085988481658748083205070504932198000988604333958'
            , $math->mod($b, $c));

    }

    public function testMul()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new BcEngine();
        $this->assertEquals(1522756, $math->mul($a, $a));
        $this->assertEquals('1523060596888771785469376020510342038779135661438916853827453718463560582690945158853917400742616234688308574558442044964',
            $math->mul($b, $b));
        $this->assertEquals('4040760497619659988396017237570892345412667279675376001385393199805230792128304954488986267360185801341164900937850776470852477066413262703893764',
            $math->mul($c, $c));
        $this->assertEquals(1, $math->mul(1, 1));
    }

    public function testPow()
    {
        $a = 1234;
        $b = '1234';
        $c = '0x4D2';
        $pow = '20';
        $math = new BcEngine();
        $this->assertEquals('67035243914691711794360082394262505332216263021359359696306176', $math->pow($a, $pow));
        $this->assertEquals('67035243914691711794360082394262505332216263021359359696306176', $math->pow($b, $pow));
        $this->assertEquals('67035243914691711794360082394262505332216263021359359696306176', $math->pow($c, $pow));
        $this->assertEquals(1, $math->pow(1, 1));
    }

    public function testSub()
    {
        $a = 1234;
        $b = '1234123412341234123412341234123412412341234213412421341342342';
        $c = '0x1234123412341234123412341234123412412341234213412421341342342';
        $math = new BcEngine();
        $this->assertEquals(0, $math->sub($a, $a));
        $this->assertEquals('1234123412341234123412341234123412412341234213412421341341108',
            $math->sub($b, $a));
        $this->assertEquals('2010164296174493893887279757032336564542916106403683105277846450565757116',
            $math->sub($c, $b));
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
