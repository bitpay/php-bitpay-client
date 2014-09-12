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

namespace Bitpay\DependencyInjection\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Used to load a configuration that is passed in as an array
 *
 * @package Bitpay
 */
class ArrayLoader extends Loader
{
    protected $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function load($resource, $type = null)
    {

        // validation
        foreach (array_keys($resource) as $namespace) {
            if (in_array($namespace, array('imports', 'paramters', 'services'))) {
                continue;
            }
            if (!$this->container->hasExtension($namespace)) {
                $extensionNamespaces = array_filter(
                    array_map(
                        function ($ext) {
                            return $ext->getAlias();
                        },
                        $this->container->getExtensions()
                    )
                );
                throw new InvalidArgumentException(sprintf(
                    'There is no extension able to load the configuration for "%s". Looked for namespace "%s", found %s',
                    $namespace,
                    $namespace,
                    $extensionNamespaces ? sprintf('"%s"', implode('", "', $extensionNamespaces)) : 'none'
                ));
            }
        }

        // Set Paramters
        if (isset($resource['parameters'])) {
            foreach ($resource['parameters'] as $key => $value) {
                $this->container->setParameter($key, $value);
            }
        }

        // extensions
        foreach ($resource as $namespace => $values) {
            if (in_array($namespace, array('imports', 'parameters', 'services'))) {
                continue;
            }

            if (!is_array($values)) {
                $values = array();
            }

            $this->container->loadFromExtension($namespace, $values);
        }
    }

    public function supports($resource, $type = null)
    {
        return is_array($resource);
    }
}
