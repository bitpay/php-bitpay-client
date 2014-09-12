<?php

namespace Bitpay\Config;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This class contains all the valid configuration settings that can be used.
 * If you update this file to add new settings, please make sure you update the
 * documentation as well.
 *
 * @package Bitpay
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{

    /**
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('bitpay');
        $rootNode
            ->children()
                ->scalarNode('api_key')
                    ->info('API Key Obtained from BitPay')
                    ->defaultNull()
                ->end()
                ->scalarNode('public_key_filename')
                    ->info('Public Key Filename')
                    ->defaultValue('/tmp/bitpay.pub')
                ->end()
                ->scalarNode('private_key_filename')
                    ->info('Private Key Filename')
                    ->defaultValue('/tmp/bitpay.pri')
                ->end()
                ->scalarNode('sin_filename')
                    ->info('Private Key Filename')
                    ->defaultValue('/tmp/bitpay.sin')
                ->end()
                ->enumNode('network')
                    ->values(array('livenet', 'testnet'))
                    ->info('Network')
                    ->defaultValue('livenet')
                ->end()
                ->enumNode('adapter')
                    ->values(array('curl', 'mock'))
                    ->info('Client Adapter')
                    ->defaultValue('curl')
                ->end()
                ->scalarNode('logger_file')
                    ->info('Location of log file')
                    ->defaultValue('/tmp/bitpay.log')
                ->end()
                ->scalarNode('logger_level')
                    ->info('Default log level')
                    ->defaultValue(\Monolog\Logger::NOTICE)
                ->end()
                ->enumNode('key_storage')
                    ->values(array('filesystem', 'mock'))
                    ->info('Where to store the keys at')
                    ->defaultValue('filesystem')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
