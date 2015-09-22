<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
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
 * Sets up container and prepares for dependency injection.
 *
 * @package Bitpay
 */
class Bitpay
{
    /**
     * @var ContainerBuilder
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
     * @param null|ContainerBuilder $container
     */
    public function __construct($config = array(), ContainerBuilder $container = null)
    {
        $this->container = $container;

        if (is_null($container)) {
            $this->initializeContainer($config);
        }
    }

    /**
     * Initialize the container
     *
     * @param array|string $config
     */
    protected function initializeContainer($config)
    {
        $this->container = $this->buildContainer($config);
        $this->container->compile();
    }

    /**
     * Build the container of services and parameters.
     * 
     * @param array|string $config
     * @return ContainerBuilder
     */
    protected function buildContainer($config)
    {
        $container = new ContainerBuilder(new ParameterBag($this->getParameters()));

        $this->prepareContainer($container);
        $this->getContainerLoader($container)->load($config);

        return $container;
    }

    /**
     * @return array<string,string>
     */
    protected function getParameters()
    {
        return array(
            'bitpay.root_dir' => realpath(__DIR__ . '/..'),
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function prepareContainer(ContainerBuilder $container)
    {
        foreach ($this->getDefaultExtensions() as $ext) {
            $container->registerExtension($ext);
            $container->loadFromExtension($ext->getAlias());
        }
    }

    /**
     * @param  ContainerBuilder $container
     * @return DelegatingLoader
     */
    private function getContainerLoader(ContainerBuilder $container)
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
     * Returns an array of the default extensions.
     *
     * @return BitpayExtension[]
     */
    private function getDefaultExtensions()
    {
        return array(
            new BitpayExtension(),
        );
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return object|null
     */
    public function get($service)
    {
        return $this->container->get($service);
    }
}
