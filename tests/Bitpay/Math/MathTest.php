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
  /**
   * @requires  extension gmp
   * @runInSeparateProcess
   */
	public function testGmpMath()
  {
    if (!extension_loaded('gmp'))
    {
      $this->markTestSkipped('The GMP extension is NOT loaded! You must enable it to run this test');
    }
    Math::add("3324234234234234234", "3324234234234234234");
    $this->assertEquals(new GmpEngine(), Math::getEngine());
	}

  /**
   * @requires  extension bcmath
   * @runInSeparateProcess
   */
	public function testBcMath()
	{
    if (!extension_loaded('bcmath'))
    {
      $this->markTestSkipped('The Bcmath extension is NOT loaded! You must enable it to run this test');
    } elseif (extension_loaded('gmp')) {
      $this->markTestSkipped('The GMP extension is loaded! You must remove it to run this test');
    }
    Math::setEngine(null);
    $message=shell_exec("sudo php5dismod gmp");
    print_r($message);
    Math::add("3324234234234234234", "3324234234234234234");
    $this->assertEquals(new BcEngine(), Math::getEngine());
  }

  /**
   * @runInSeparateProcess
   */
	public function testRpMath()
  {
    if (extension_loaded('gmp'))
    {
      $this->markTestSkipped('The GMP extension is loaded! You must remove it to run this test');
    } elseif (extension_loaded('bcmath'))
    {
      $this->markTestSkipped('The Bcmath extension is loaded! You must remove it to run this test');
    }
    Math::setEngine(null);
    Math::add("3324234234234234234", "3324234234234234234");
    $this->assertEquals(new RpEngine(), Math::getEngine());
  }

  public function testenablemods()
  {
    $message=shell_exec("sudo php5enmod gmp");
    print_r($message);
  }
}
