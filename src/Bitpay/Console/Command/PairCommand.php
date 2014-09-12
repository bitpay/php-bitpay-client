<?php

namespace Bitpay\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Bitpay\Bitpay;

/**
 */
class PairCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('pair')
            ->setDescription('Recieve a token for the crytographically secure bitpay api')
            ->setDefinition(
                array(
                    new InputArgument('pairingcode', InputArgument::REQUIRED, 'Pairing code from your account'),
                )
            )
            ->setHelp(
<<<HELP

<comment>Examples:</comment>

    %command.full_name% 2g1h33

HELP
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $publicKey  = $input->getOption('home') . '/api.key';
        $privateKey = $input->getOption('home') . '/api.pub';
        if (!file_exists($publicKey) || !file_exists($privateKey)) {
            throw new \Exception(
                sprintf(
                    'API keys could not be found in "%s"',
                    $input->getOption('home')
                )
            );
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sin    = file_get_contents($input->getOption('home') . '/api.pub');
        $bitpay = new Bitpay($input->getOption('home') . '/config.yml');
        $client = $bitpay->get('client');
        $payload = array(
            'id'          => $sin,
            'pairingCode' => $input->getArgument('pairingcode'),
            'label'       => 'php-bitpay-client',
        );

        $token = $client->createToken($payload);
        var_dump($token);
    }
}
