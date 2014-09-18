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
                    ->defaultValue(getenv('HOME').'/.bitpay/bitpay.pub')
                ->end()
                ->scalarNode('private_key')
                    ->info('Private Key Filename')
                    ->defaultValue(getenv('HOME').'/.bitpay/bitpay.pri')
                ->end()
                ->scalarNode('sin_key')
                    ->info('Private Key Filename')
                    ->defaultValue(getenv('HOME').'/.bitpay/bitpay.sin')
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
                    ->defaultValue(getenv('HOME').'/.bitpay/bitpay.log')
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
