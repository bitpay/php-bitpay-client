<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
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
 * @package Bitpay
 */
class Configuration implements ConfigurationInterface
{
    private $pubfilename = '/.bitpay/api.pub';
    private $prifilename = '/.bitpay/api.key';
    private $sinfilename = '/.bitpay/api.sin';
    private $defstorage  = 'Bitpay\Storage\EncryptedFilesystemStorage';
    private $networks    = array('livenet', 'testnet');
    private $adapters    = array('curl', 'mock');

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('bitpay');

        $rootNode
            ->children()
                ->scalarNode('public_key')
                    ->info('Public Key Filename')
                    ->defaultValue($this->getPubKeyFilename())
                ->end()
                ->scalarNode('private_key')
                    ->info('Private Key Filename')
                    ->defaultValue($this->getPriKeyFilename())
                ->end()
                ->scalarNode('sin_key')
                    ->info('SIN Filename')
                    ->defaultValue($this->getSinFilename())
                ->end()
                ->enumNode('network')
                    ->values($this->networks)
                    ->info('Network')
                    ->defaultValue($this->networks[0])
                ->end()
                ->enumNode('adapter')
                    ->values($this->adapters)
                    ->info('Client Adapter')
                    ->defaultValue($this->adapters[0])
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
     * Adds the key_storage node with validation rules.
     * key_storage MUST:
     *     * implement Bitpay\Storage\StorageInterface
     *     * be a class that can be loaded
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     * @throws \Exception
     */
    protected function addKeyStorageNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('key_storage', 'scalar');

        $node
            ->info('Class that is used to store your keys')
            ->defaultValue($this->defstorage)
            ->validate()
                ->always()
                ->then(function ($value) {
                    if (!class_exists($value)) {
                        throw new \Exception(
                            sprintf(
                                'Could not find class "%s".',
                                $value
                            )
                        );
                    }

                    // requires PHP >= 5.3.7
                    if (is_subclass_of($value, 'Bitpay\Storage\StorageInterface') === false) {
                        throw new \Exception('[ERROR] In Configuration::addKeyStorageNode(): "' . $value . '" does not implement "Bitpay\Storage\StorageInterface".');
                    }

                    return $value;
                })
            ->end();

        return $node;
    }

    /**
     * @return string
     */
    private function getPubKeyFilename()
    {
        return getenv('HOME') . $this->pubfilename;
    }

    /**
     * @return string
     */
    private function getPriKeyFilename()
    {
        return getenv('HOME') . $this->prifilename;
    }

    /**
     * @return string
     */
    private function getSinFilename()
    {
        return getenv('HOME') . $this->sinfilename;
    }
}
