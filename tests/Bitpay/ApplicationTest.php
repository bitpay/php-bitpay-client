<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 BitPay, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Bitpay;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    public function testGetUsers()
    {
        $application = new Application();

        $this->assertInternalType('array', $application->getUsers());
        $this->assertEmpty($application->getUsers());
    }

    /**
     * @depends testGetUsers
     */
    public function testAddUser()
    {
        $application = new Application();
        $application->addUser($this->getMockUser());

        $this->assertInternalType('array', $application->getUsers());
        $this->assertCount(1, $application->getUsers());
    }

    public function testGetOrgs()
    {
        $application = new Application();

        $this->assertInternalType('array', $application->getOrgs());
        $this->assertEmpty($application->getOrgs());
    }

    /**
     * @depends testGetOrgs
     */
    public function testAddOrg()
    {
        $application = new Application();
        $application->addOrg($this->getMockOrg());

        $this->assertInternalType('array', $application->getOrgs());
        $this->assertCount(1, $application->getOrgs());
    }

    private function getMockUser()
    {
        return $this->getMock('Bitpay\UserInterface');
    }

    private function getMockOrg()
    {
        return $this->getMock('Bitpay\OrgInterface');
    }
}
