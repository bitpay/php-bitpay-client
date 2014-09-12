<?php

namespace Bitpay;

/**
 * Creates an application for a new merchant account
 *
 * @package Bitpay
 */
interface ApplicationInterface
{

    /**
     * @return array
     */
    public function getUsers();

    /**
     * @return array
     */
    public function getOrgs();
}
