<?php


function newSession()
{
	$phantomjsDriver = new \Behat\Mink\Driver\Selenium2Driver('phantomJS');

    // Setup mink sessions.
    $phantomjsSession = new \Behat\Mink\Session($phantomjsDriver);

    // Setup mink session manager.
    $mink = new \Behat\Mink\Mink();

    $mink->registerSession('phantomjs', $phantomjsSession);

    $mink->setDefaultSessionName('phantomjs');

    return $mink->getSession();
}
