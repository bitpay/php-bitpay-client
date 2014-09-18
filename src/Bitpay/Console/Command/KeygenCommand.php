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

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Bitpay\PrivateKey;

/**
 * Command used to generate keypairs
 */
class KeygenCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('keygen')
            ->setDescription('generate key pair to associate with your bitpay user')
            ->setDefinition(
                array(
                    new InputOption('password', null, InputOption::VALUE_OPTIONAL, 'Key Password'),
                    new InputOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite keys if found'),
                )
            )
            ->setHelp(
<<<HELP

This command is used to generate keys that are used to connect to your BitPay
account. Once you create your keys, they are saved in the directory <info>\$HOME/.bitpay/</info>.

<comment>Note</comment>

This command CHMOD's your keys to 0600 for the private key and 0644 for the
public key. Please keep these safe since these keys can compromise your
account.

<comment>Examples:</comment>

    Generate Keys Interactively:

        %command.full_name%


    Quickly generate keys:

        %command.full_name% -n

HELP
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        /**
         * Check and see if the keys have been found, if overwrite is not true
         * the command needs to exit
         */
        if (!$input->getOption('overwrite')) {
            $publicKey  = $input->getOption('home') . '/api.pub';
            $privateKey = $input->getOption('home') . '/api.key';
            if (file_exists($publicKey) || file_exists($privateKey)) {
                throw new \Exception(
                    sprintf(
                        'Keys already exist in "%s", if you want to overwrite them, please pass the --overwrite option',
                        $input->getOption('home')
                    )
                );
            }
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        $password = $dialog->askHiddenResponse(
            $output,
            'Key Password (optional): ',
            false
        );

        if ($password) {
            $passwordCheck = $dialog->askHiddenResponseAndValidate(
                $output,
                'Verify Key Password: ',
                function ($value) use ($password) {
                    if ($value != $password) {
                        throw new \Exception('Passwords did not match');
                    }

                    return $value;
                },
                1 // There can only be one
            );

            $input->setOption('password', $password);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Generating Keys...</info>');

        $private = new \Bitpay\PrivateKey($input->getOption('home').'/api.key');
        $public  = new \Bitpay\PublicKey($input->getOption('home').'/api.pub');
        $private->generate();
        $public->setPrivateKey($private);
        $public->generate();

        $manager = $this->container->get('key_manager');
        $manager->persist($private);
        $manager->persist($public);

        chmod($input->getOption('home') . '/api.key', 0600);
        chmod($input->getOption('home') . '/api.pub', 0644);

        $output->writeln(
            array(
                sprintf('<info>Keys saved to "<comment>%s</comment>"</info>', $input->getOption('home')),
            )
        );
    }
}
