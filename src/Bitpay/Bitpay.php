<?php
/**
 * The MIT License (MIT)
 * 
 * Copyright (c) 2014 BitPay, Inc.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Bitpay;

use Bitpay\DependencyInjection\BitpayExtension;
use Bitpay\DependencyInjection\Loader\ArrayLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Setups container and is ready for some dependency injection action
 *
 * @package Bitpay
 */
class Bitpay
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * First argument can either be a string or fullpath to a yaml file that
     * contains configuration parameters. For a list of configuration values
     * see \Bitpay\Config\Configuration class
     *
     * The second argument is the container if you want to build one by hand.
     *
     * @param array|string       $config
     * @param ContainerInterface $container
     */
    public function __construct($config = array(), ContainerInterface $container = null)
    {
        $this->container = $container;

        if (is_null($container)) {
            $this->initializeContainer($config);
        }
    }

    /**
     * Initialize the container
     */
    protected function initializeContainer($config)
    {
        $this->container = $this->buildContainer($config);
        $this->container->compile();
    }

    /**
     * Build the container of services and parameters
     */
    protected function buildContainer($config)
    {
        $container = new ContainerBuilder(new ParameterBag($this->getParameters()));
        $this->prepareContainer($container);
        $this->getContainerLoader($container)->load($config);

        return $container;
    }

    protected function getParameters()
    {
        return array(
            'bitpay.root_dir' => realpath(__DIR__ . '/..'),
        );
    }

    /**
     */
    private function prepareContainer(ContainerInterface $container)
    {
        foreach ($this->getDefaultExtensions() as $ext) {
            $container->registerExtension($ext);
            $container->loadFromExtension($ext->getAlias());
        }
    }

    /**
     * @param ContainerInterface $container
     * @return LoaderInterface
     */
    private function getContainerLoader(ContainerInterface $container)
    {
        $locator  = new FileLocator();
        $resolver = new LoaderResolver(
            array(
                new ArrayLoader($container),
                new YamlFileLoader($container, $locator),
            )
        );

        return new DelegatingLoader($resolver);
    }

    /**
     * Returns an array of the default extensions
     *
     * @return array
     */
    private function getDefaultExtensions()
    {
        return array(
            new BitpayExtension(),
        );
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function get($service)
    {
        return $this->container->get($service);
    }
}
