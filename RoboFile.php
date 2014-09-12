<?php

require_once __DIR__ . '/vendor/autoload.php';

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
}
