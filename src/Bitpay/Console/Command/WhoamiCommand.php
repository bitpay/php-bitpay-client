<?php

namespace Bitpay\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WhoamiCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('whoami')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
