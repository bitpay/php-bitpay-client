<?php

namespace Bitpay\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UnpairCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('unpair')
            ->setDescription('invalidate client identity from your bitpay user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
