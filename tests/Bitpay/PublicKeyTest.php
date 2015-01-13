<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @see https://github.com/bitpay/bitcore/blob/master/test/test.Key.js
 */
class PublicKeyTest extends \PHPUnit_Framework_TestCase
{

    private $hexKeys = array(
        array(
            'private' => '10a2036fd1c8f7aeae1e21cd2a11bb9654f76844d1636809618b5e2cbb00c35d',
            'public'  => '031530ff12958742b22bf4a764e776db2e831b10b8177ea20460b89841256a24e9',
            'pub_x'   => '1530ff12958742b22bf4a764e776db2e831b10b8177ea20460b89841256a24e9',
            'pub_y'   => '2b5a428b0fa573ec26a69f528f327571dc0235657869617f0e12b68cd0554dd5'
        ),
        array(
            'private' => 'e7723af2179ab2cfaf57508aae8ff7647d65ce55d59b0d482b4eec963930c421',
            'public'  => '023718fcacdc89d213ab14b22bfe7babc335da572f6020ac60de65f80309cd146d',
            'pub_x'   => '3718fcacdc89d213ab14b22bfe7babc335da572f6020ac60de65f80309cd146d',
            'pub_y'   => 'aae37beb4cd64ed0524d1441f2d8b1b75ddfb89e6e356f89b6f15cd6aec16146'
        ),
        array(
            'private' => 'fd7c6914790d3bbf3184d9830e3f1a327e951e3478dd0b28f0fd3b0e774bbd68',
            'public'  => '038fcc587b8dcca0f8bfc2651895dddaa479162abcd56e1d72d09db0a96c50a1f7',
            'pub_x'   => '8fcc587b8dcca0f8bfc2651895dddaa479162abcd56e1d72d09db0a96c50a1f7',
            'pub_y'   => '757c5a35fe6a1cd2263ee9a833c7b46c0d7194cf6a8771e2b35b3bbb47240c97'
        ),
        array(
            'private' => 'b9873126380d2133edc00d709c5fb2230da121a4092592bed96c77ecfc4fd92a',
            'public'  => '036bf5247bde7bfeee6a53da24a301332df2392db0de2e63757b795d07dec00fad',
            'pub_x'   => '6bf5247bde7bfeee6a53da24a301332df2392db0de2e63757b795d07dec00fad',
            'pub_y'   => 'f1a7118bc9fab555924d3486bf6344ffb319e6424f40e8a424e07bae781cc893'
        ),
        array(
            'private' => '109ae03bf6654452d6b8fdaaa8efabccdc51c0e014bbf2bf3dd0fb0dc5443437',
            'public'  => '03cf6db35e2a8149841cf85181f89e274c29bb3f454f61c97ce0c1352791516f4e',
            'pub_x'   => 'cf6db35e2a8149841cf85181f89e274c29bb3f454f61c97ce0c1352791516f4e',
            'pub_y'   => '123045e499c2c97310167ae0f4107c4bfd47dfb826c707a1f4f80c7835d1f635'
        ),
        array(
            'private' => 'f3c3a0a5696e4ca4218d6ebe05d3644f57d860d0b7aaf5369a47dd1eecf4850e',
            'public'  => '02efa5c75a2e1ac5e958045681ea81ac0c132c18f09f49ad12284b938658d305f2',
            'pub_x'   => 'efa5c75a2e1ac5e958045681ea81ac0c132c18f09f49ad12284b938658d305f2',
            'pub_y'   => '08291958f3f0eb8b5eb39edee5c2a043ba53d2918caa949732ffd88ed4dd843e'
        ),
        array(
            'private' => 'bf69a0ce9606ca657b4d7fc5c8cf58fd90bb9bb3441bc62dd0e7329d6147c0bf',
            'public'  => '02c31cc1f146e85a1db6539ab0a596d625b3b785fe2536733c799b0648a18f1e9e',
            'pub_x'   => 'c31cc1f146e85a1db6539ab0a596d625b3b785fe2536733c799b0648a18f1e9e',
            'pub_y'   => '3bb303944f18b4a9e7629870e38b35988989e39b957ae0f04247aa8be8bc094c'
        ),
        array(
            'private' => '3be6d79171d4fe7532a3052141397b248919ad8181b99c0df5cf8932802aa959',
            'public'  => '0306b5787b5112cb8c56005b325da6e82ca890d617a5d6797c3b39a758e67a1483',
            'pub_x'   => '06b5787b5112cb8c56005b325da6e82ca890d617a5d6797c3b39a758e67a1483',
            'pub_y'   => 'ac55297820d5f4ce02f640860a783e8c364e1778000c6600306b0fb13ea6d829'
        ),
        array(
            'private' => 'd5ed1fb84d65167d746eb8dc17e63e7b1f7b84182760b839a4d001e767239d6e',
            'public'  => '02c9bcd9ee0fe7c69d6fe76fdc0e6f2ebd7165673ebb1c2cfa3f0497fa6117714a',
            'pub_x'   => 'c9bcd9ee0fe7c69d6fe76fdc0e6f2ebd7165673ebb1c2cfa3f0497fa6117714a',
            'pub_y'   => '1a73cef26e65cda5a057bb73b22e32c452d3b732663c5826af10fa4310e498c2'
        ),
        array(
            'private' => 'a9ebade62798200565f82ec01a04f614592704e3ea5a3c7907b94c0e8a9dd942',
            'public'  => '03784b25686041b7ea7aa4c94fb1f56b5c1515d1f358895adaa8d9a824377cc3a2',
            'pub_x'   => '784b25686041b7ea7aa4c94fb1f56b5c1515d1f358895adaa8d9a824377cc3a2',
            'pub_y'   => '72a931ef1ecc18bb7111d5d9bf039a9b9219c75c6f7939f7b8b54a121d7bac9f'
        )
    );

    public function testId()
    {
        $key = new PublicKey('/path/to/key.pub');
        $this->assertSame('/path/to/key.pub', $key->getId());
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Bitpay\PublicKey', PublicKey::create());
    }

    public function testGenerate()
    {
        foreach($this->hexKeys as $hexKey) {
            $pubKey = new PublicKey();
            $pubKey->setPrivateKey($this->getMockPrivateKey($hexKey['private']));
            $pubKey->generate();
            $this->assertEquals($hexKey['public'], (string) $pubKey);
        }
    }

    public function testGenerateOnlyOnce()
    {
        $key = new PublicKey();
        $key->setPrivateKey($this->getMockPrivateKey());
        $key->generate();

        $hexValue = $key->getHex();

        $key->generate();

        // Make sure values do not change
        $this->assertSame(
            $hexValue,
            $key->getHex()
        );
    }

    /**
     * @depends testGenerate
     */
    public function testGetHex()
    {
        $pubKey = new PublicKey();
        $pubKey->setPrivateKey($this->getMockPrivateKey());
        $this->assertNull($pubKey->getHex());
        $pubKey->generate();
        $this->assertEquals(128, strlen($pubKey->getHex()));
    }

    /**
     * @depends testGenerate
     */
    public function testGetDec()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey($this->getMockPrivateKey());

        $this->assertNull($pubKey->getDec());

        $pubKey->generate();
        $this->assertGreaterThanOrEqual(154, strlen($pubKey->getDec()));
    }

    /**
     * @see https://github.com/bitpay/bitcore/blob/master/test/test.Key.js
     * @depends testGenerate
     */
    public function testToString()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $this->assertSame('', (string) $pubKey);

        $pubKey->generate(PrivateKey::create()->generate());

        if ('02'.$pubKey->getX() == $pubKey) {
            $compressed = '02'.$pubKey->getX();
        } else {
            $compressed = '03'.$pubKey->getX();
        }

        $this->assertSame($compressed, (string) $pubKey);

        $this->assertEquals(66, strlen((string) $pubKey));
    }

    public function testGetX()
    {
        foreach($this->hexKeys as $hexKey) {
            $pubKey = new PublicKey();
            $pubKey->setPrivateKey($this->getMockPrivateKey($hexKey['private']));
            $pubKey->generate();
            $this->assertEquals($hexKey['pub_x'], $pubKey->getX());
        }
    }

    public function testGetY()
    {
        foreach($this->hexKeys as $hexKey) {
            $pubKey = new PublicKey();
            $pubKey->setPrivateKey($this->getMockPrivateKey($hexKey['private']));
            $pubKey->generate();
            $this->assertEquals($hexKey['pub_y'], $pubKey->getY());
        }
    }

    public function testCreateFromPrivateKey()
    {
        $key = PublicKey::createFromPrivateKey($this->getMockPrivateKey());
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }

    public function testIsValid()
    {
        $key = new PublicKey();
        $this->assertFalse($key->isValid());
        $key->setPrivateKey($this->getMockPrivateKey());
        $key->generate();
        $this->assertTrue($key->isValid());
    }

    public function testGetSin()
    {
        $pub = new PublicKey();
        $pub->setPrivateKey($this->getMockPrivateKey());
        $sin = $pub->getSin();

        $this->assertInstanceOf('Bitpay\SinKey', $sin);
    }

    public function testGetSinOnlyOnce()
    {
        $pub = new PublicKey();
        $pub->setPrivateKey($this->getMockPrivateKey());

        $sin = $pub->getSin();

        $this->assertSame(
            $sin,
            $pub->getSin()
        );
    }

    public function testIsGenerated()
    {
        $pub = new PublicKey();
        $pub->setPrivateKey($this->getMockPrivateKey());
        $this->assertFalse($pub->isGenerated());
        $pub->generate();
        $this->assertTrue($pub->isGenerated());
    }

    private function getMockPrivateKey($hex = null)
    {
        $hex = ($hex === null) ? $this->hexKeys[0]['private'] : $hex;
        $key = $this->getMock('Bitpay\PrivateKey');
        $key->method('isValid')->will($this->returnValue(true));

        $key
            ->method('getHex')
            ->will($this->returnValue($hex));
            
        return $key;
    }

}
