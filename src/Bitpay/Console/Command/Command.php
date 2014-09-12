<?php

namespace Bitpay\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{

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
