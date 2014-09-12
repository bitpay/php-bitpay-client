<?php

namespace Bitpay;

use Bitpay\DependencyInjection\BitpayExtension;
use Bitpay\DependencyInjection\Loader\ArrayLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Setups container and is ready for some dependency injection action
 *
 * @package Bitpay
 */
class Bitpay extends ContainerAware
{

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
        if (is_null($container)) {
            $container = new ContainerBuilder();
            $this->registerAndLoadExtensions($container);
            $this->buildLoader($container)->load($config);
            $container->compile();
        }

        $this->setContainer($container);
    }

    /**
     * @param ContainerInterface $container
     */
    private function registerAndLoadExtensions(ContainerInterface $container)
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
    private function buildLoader(ContainerInterface $container)
    {
        return new DelegatingLoader(
            new LoaderResolver(
                array(
                    new ArrayLoader($container),
                    new YamlFileLoader($container, new FileLocator()),
                )
            )
        );
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
