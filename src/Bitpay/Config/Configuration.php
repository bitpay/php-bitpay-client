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
 * @see http://symfony.com/doc/current/components/config/definition.html
 *
 * @package Bitpay
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
                ->append($this->addKeyStorageNode())
                ->scalarNode('key_storage_password')
                    ->info('Used to encrypt and decrypt keys when saving to filesystem')
                    ->defaultNull()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * Adds the key_storage node with validation rules
     *
     * key_storage MUST:
     *     * implement Bitpay\Storage\StorageInterface
     *     * be a class that can be loaded
     */
    protected function addKeyStorageNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('key_storage', 'scalar');

        $node
            ->info('Class that is used to store your keys')
            ->defaultValue('Bitpay\Storage\EncryptedFilesystemStorage')
            ->validate()
                ->ifTrue(function ($value) {
                    return (false === class_exists($value));
                })
                ->thenInvalid('Could not find class "%s".')
            ->end()
            ->validate()
                ->ifTrue(function ($value) {
                    return (false === is_subclass_of($value, 'Bitpay\Storage\StorageInterface'));
                })
                ->thenInvalid('"%s" does not implement "Bitpay\Storage\StorageInterface"')
            ->end();

        return $node;
    }
}
