<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
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
