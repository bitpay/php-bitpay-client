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

namespace Bitpay\Util;

class GmpTest extends \PHPUnit_Framework_TestCase
{

    public function testDoubleAndAdd()
    {
    }

    private function getTestData()
    {
        return array(
            // test vectors: n, x, y
            array(
                'AA5E28D6A97A2479A65527F7290311A3624D4CC0FA1578598EE3C2613BF99522',
                '34F9460F0E4F08393D192B3C5133A6BA099AA0AD9FD54EBCCFACDFA239FF49C6',
                '0B71EA9BD730FD8923F6D25A7A91E7DD7728A960686CB5A901BB419E0F2CA232'
            ),
            array(
                '7E2B897B8CEBC6361663AD410835639826D590F393D90A9538881735256DFAE3',
                'D74BF844B0862475103D96A611CF2D898447E288D34B360BC885CB8CE7C00575',
                '131C670D414C4546B88AC3FF664611B1C38CEB1C21D76369D7A7A0969D61D97D'
            ),
            array(
                '6461E6DF0FE7DFD05329F41BF771B86578143D4DD1F7866FB4CA7E97C5FA945D',
                'E8AECC370AEDD953483719A116711963CE201AC3EB21D3F3257BB48668C6A72F',
                'C25CAF2F0EBA1DDB2F0F3F47866299EF907867B7D27E95B3873BF98397B24EE1'
            ),
            array(
                '376A3A2CDCD12581EFFF13EE4AD44C4044B8A0524C42422A7E1E181E4DEECCEC',
                '14890E61FCD4B0BD92E5B36C81372CA6FED471EF3AA60A3E415EE4FE987DABA1',
                '297B858D9F752AB42D3BCA67EE0EB6DCD1C2B7B0DBE23397E66ADC272263F982'
            ),
            array(
                '1B22644A7BE026548810C378D0B2994EEFA6D2B9881803CB02CEFF865287D1B9',
                'F73C65EAD01C5126F28F442D087689BFA08E12763E0CEC1D35B01751FD735ED3',
                'F449A8376906482A84ED01479BD18882B919C140D638307F0C0934BA12590BDE'
            ),
        );
    }
}
