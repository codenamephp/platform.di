<?php
/**
 * Copyright 2018 Bastian Schwarz <bastian@codename-php.de>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace de\codenamephp\platform\di;

use DI\Definition\Source\MutableDefinitionSource;
use Psr\Container\ContainerInterface;

/**
 *
 * @author Bastian Schwarz <bastian@codename-php.de>
 */
class Container extends \DI\Container implements iContainer {

  /**
   *
   * @param MutableDefinitionSource|null $definitionSource
   * @param \DI\Proxy\ProxyFactory|null $proxyFactory
   * @param ContainerInterface|null $wrapperContainer
   *
   * @since 3.0 Type of $definitionSource was changed to \DI\Definition\Source\MutableDefinitionSource and type of $wrapperContainer was changed to
   *   \Psr\Container\ContainerInterface
   * @since 3.0 All parameters are now optional
   */
  public function __construct(MutableDefinitionSource $definitionSource = null, \DI\Proxy\ProxyFactory $proxyFactory = null, ContainerInterface $wrapperContainer = null) {
    parent::__construct($definitionSource, $proxyFactory, $wrapperContainer);

    $this->set(static::class, $this);
    $this->set(iContainer::class, $this);
  }

  /**
   * {@inheritdoc}
   *
   * @since 3.0 Type string of $name is now enforced
   */
  public function set(string $name, $value) {
    parent::set($name, $value);
    return $this;
  }
}
