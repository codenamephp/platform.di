<?php declare(strict_types=1);
/**
 * Copyright 2020 Bastian Schwarz <bastian@codename-php.de>.
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
use DI\Proxy\ProxyFactory;
use Psr\Container\ContainerInterface;

final class Container extends \DI\Container implements iContainer {

  /**
   * @param null|MutableDefinitionSource $definitionSource The default definitions source
   * @param null|ProxyFactory $proxyFactory The factory to create the procxies with
   * @param ContainerInterface|null $wrapperContainer if the container is wrapped by another container
   */
  public function __construct(MutableDefinitionSource $definitionSource = null, ProxyFactory $proxyFactory = null, ContainerInterface $wrapperContainer = null) {
    parent::__construct($definitionSource, $proxyFactory, $wrapperContainer);

    $this->set(iContainer::class, $this);
  }
}
