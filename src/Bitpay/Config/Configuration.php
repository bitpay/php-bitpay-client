<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

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
                ->scalarNode('public_key')
                    ->info('Public Key Filename')
                    ->defaultValue(getenv('HOME').'/.bitpay/api.pub')
                ->end()
                ->scalarNode('private_key')
                    ->info('Private Key Filename')
                    ->defaultValue(getenv('HOME').'/.bitpay/api.key')
                ->end()
                ->scalarNode('sin_key')
                    ->info('Private Key Filename')
                    ->defaultValue(getenv('HOME').'/.bitpay/api.sin')
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
                ->enumNode('key_storage')
                    ->values(array('filesystem', 'mock', 'encrypted_filesystem'))
                    ->info('Where to store the keys at')
                    ->defaultValue('encrypted_filesystem')
                ->end()
                ->scalarNode('key_storage_password')
                    ->info('Used to encrypt and decrypt keys when saving to filesystem')
                    ->defaultNull()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
