<?php
/*
 * Copyright 2015 Bastian Schwarz <bastian@codename-php.de>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @namespace
 */
namespace de\codenamephp\platform\di;

/**
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class Container extends \DI\Container implements iContainer {

  public function __construct(\DI\Definition\Source\DefinitionSource $definitionSource, \DI\Proxy\ProxyFactory $proxyFactory, \Interop\Container\ContainerInterface $wrapperContainer = null) {
    parent::__construct($definitionSource, $proxyFactory, $wrapperContainer);

    $this->set(static::class, $this);
    $this->set(iContainer::class, $this);
  }

  /**
   * {@inheritdoc}
   */
  public function set($name, $value) {
    parent::set($name, $value);
    return $this;
  }
}
