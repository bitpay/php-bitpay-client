<?php

namespace Bitpay\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;

class Application extends BaseApplication
{
    const VERSION = '2.0.0';

    public function __construct()
    {
        parent::__construct('BitPay CLI', self::VERSION);

        $this->getDefinition()->addOptions(
            array(
                new InputOption('--home', null, InputOption::VALUE_REQUIRED, 'Directory where generated files are'),
                new InputOption('--config', '-c', InputOption::VALUE_REQUIRED, 'Configuration file to use'),
            )
        );
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new Command\ConfigCommand();
        $commands[] = new Command\KeygenCommand();
        $commands[] = new Command\PairCommand();
        $commands[] = new Command\UnpairCommand();
        $commands[] = new Command\WhoamiCommand();

        return $commands;
    }
}
