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

namespace de\codenamephp\platform\di\definitionsProvider\collection;

use de\codenamephp\platform\di\definitionsProvider\iDefinitionsProvider;

/**
 * A collection that stores the providers in an array without any kind of checks, sorting etc.
 */
final class SimpleArray implements iCollection {

  /**
   * The collection of providers
   *
   * @var iDefinitionsProvider[]
   */
  private array $providers = [];

  /**
   * Gets the collection of providers which in this case is just a getter
   *
   * @return iDefinitionsProvider[]
   */
  public function get() : array {
    return $this->providers;
  }

  /**
   * Adds the given provider to the array without checking if it already exists
   *
   * @param iDefinitionsProvider $provider The provider to add to the collection
   * @return $this
   */
  public function add(iDefinitionsProvider $provider) : iCollection {
    $this->providers[] = $provider;
    return $this;
  }
}