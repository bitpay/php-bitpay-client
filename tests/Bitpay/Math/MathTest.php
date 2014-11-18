<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

function function_exists($func)
{
	return false;
}

class MathTest extends \PHPUnit_Framework_TestCase
{
	public function testIsEngineSet()
	{
		$this->assertNull(Math::getEngine());
		$engine = $this->getMock('Bitpay\Math\EngineInterface');
		Math::setEngine($engine);
		$this->assertInstanceOf('Bitpay\Math\EngineInterface', Math::getEngine());
	}

	public function testcallStatic()
	{
		Math::getEngine()->expects($this->any())
             ->method('add')
             ->will($this->returnValue(2));
        $output = Math::add(1,1);
		$this->assertEquals(2, $output);

		$ref = new \ReflectionProperty('Bitpay\Math\Math', 'engine');
		$ref->setAccessible(true);
        $ref->setValue(null);
        $ref->setAccessible(false);
        $output = Math::add(1,1);
		$this->assertEquals(2, $output);
	}

}