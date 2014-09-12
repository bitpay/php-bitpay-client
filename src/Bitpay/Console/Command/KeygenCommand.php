<?php

namespace Bitpay\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Bitpay\Bitauth;

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
            $publicKey  = $input->getOption('home') . '/api.key';
            $privateKey = $input->getOption('home') . '/api.pub';
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
        $output->writeln('Generating keys');

        $bitauth = new Bitauth();
        $keys    = $bitauth->generateSin();
        $secret  = $bitauth->encrypt($input->getOption('password'), $keys['private']);

        file_put_contents($input->getOption('home') . '/api.key', $secret);
        file_put_contents($input->getOption('home') . '/api.pub', (string) $keys['sin']);
        chmod($input->getOption('home') . '/api.key', 0600);
        chmod($input->getOption('home') . '/api.pub', 0644);

        $output->writeln(
            array(
                sprintf('Keys saved to "%s"', $input->getOption('home')),
                sprintf('Your client identifier is "%s"', $keys['sin']),
            )
        );
    }
}
