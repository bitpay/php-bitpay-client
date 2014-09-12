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

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Finder\Finder;

class RoboFile extends \Robo\Tasks
{

    public function phpunit()
    {
        $this->taskDeleteDir(__DIR__ . '/build/docs/code-coverage')->run();
        $this->taskExec('mkdir -pv ' . __DIR__ . '/build/docs/code-coverage')->run();
        $this->taskExec(__DIR__ . '/bin/phpunit')
            ->args('-c build/')
            ->run();
    }

    /**
     * Builds an archive file used for distribution
     */
    public function builddist()
    {
        $this->say('Building Release ' . file_get_contents('VERSION'));
        $this->taskDeleteDir(__DIR__ . '/build/dist')->run();
        $this->taskExec('mkdir -pv ' . __DIR__ . '/build/dist')->run();

        $phar = $this->taskPackPhar(__DIR__ . '/build/dist/bitpay.phar')
            ->compress()
            ->stub('build/stub.php');

        $finder = Finder::create()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('Tests')
            ->exclude('Test')
            ->in('src/')
            ->in('vendor/monolog/monolog/src')
            ->in('vendor/psr/log/')
            ->in('vendor/symfony/config')
            ->in('vendor/symfony/console')
            ->in('vendor/symfony/dependency-injection')
            ->in('vendor/symfony/event-dispatcher')
            ->in('vendor/symfony/filesystem')
            ->in('vendor/symfony/finder')
            ->in('vendor/symfony/process')
            ->in('vendor/symfony/yaml');

        foreach ($finder as $file) {
            //printf("src/%s, %s\n", $file->getRelativePathname(), $file->getRealPath());
            //$phar->addFile('src/' . $file->getRelativePathname(), $file->getRealPath());
            $phar->addStripped('src/' . $file->getRelativePathname(), $file->getRealPath());
        }

        $phar->addFile('bin/bitpay', 'bin/bitpay');

        $phar->run();
    }
}
