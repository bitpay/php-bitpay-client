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

namespace Bitpay\Console\Command;

use Bitpay\Bitpay;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{

    protected $container;

    /**
     * Used to make sure configuration is available
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // Make sure the home directory is set
        if (!$input->getOption('home')) {
            $input->setOption('home', getenv('HOME') . '/.bitpay');
        }

        // Create directory or display error
        if (!is_dir($input->getOption('home'))) {
            $output->writeln('<comment>Creating configuration directory: ' . $input->getOption('home') . '</comment>');
            $this->makeDirectory($input->getOption('home'));
        }

        if (!$input->getOption('config')) {
            $input->setOption('config', getenv('HOME') . '/.bitpay/config.yml');
        }

        // Make sure the config file is there
        if (!file_exists($input->getOption('config'))) {
            $output->writeln(
                sprintf('<comment>Creating config file: "%s"</comment>', $input->getOption('config'))
            );
            $this->makeDirectory(pathinfo($input->getOption('config'), PATHINFO_DIRNAME));
            if (!touch($input->getOption('config'))) {
                throw new \Exception(
                    sprintf('Could not create config "%s"', $input->getOption('config'))
                );
            }
        }

        /**
         * Find a better way to do this
         */
        $bitpay = new BitPay($input->getOption('config'));
        $this->container = $bitpay->getContainer();
    }

    /**
     * @param string $dir Directory to create
     *
     * @return boolean
     */
    protected function makeDirectory($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir)) {
                throw new \Exception(
                    sprintf('Please check permissions. Could not create directory "%s"', $dir)
                );
            }
        }
    }
}
