<?php

namespace Bitpay\DependencyInjection;

use Bitpay\Config\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 *
 * @package Bitpay
 */
class BitpayExtension implements ExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $configs);

        foreach (array_keys($config) as $key) {
            if (in_array($key, array('network'))) {
                continue;
            }
            $container->setParameter('bitpay.' . $key, $config[$key]);
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('services.xml');

        $container->setParameter('network.class', 'Bitpay\Network\\' . ucfirst($config['network']));
        $container->setParameter('adapter.class', 'Bitpay\Client\Adapter\\' . ucfirst($config['adapter']) . 'Adapter');
        $container->setParameter('logger.level', $config['logger_level']);
        $container->setParameter('logger.file', $config['logger_file']);
        $container->setParameter('key_storage.class', 'Bitpay\Storage\\' . ucfirst($config['key_storage']) . 'Storage');
    }

    public function getAlias()
    {
        return 'bitpay';
    }

    public function getNamespace()
    {
        return 'http://example.org/schema/dic/bitpay';
    }

    public function getXsdValidationBasePath()
    {
        return false;
    }
}
