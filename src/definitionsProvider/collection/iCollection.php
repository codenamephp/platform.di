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

use de\codenamephp\platform\di\definitionsProvider\dependency\CircularDependencyException;
use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;
use de\codenamephp\platform\di\definitionsProvider\iDefintionsProvider;

/**
 * Interface to collect providers. Implementations can choose to e.g. sort, modify or whatever other steps needed for the collection.
 *
 * @since 5.0
 */
interface iCollection {

  /**
   * Gets the collection of providers
   *
   * @return iDefintionsProvider[]
   * @throws MissingDependencyException if one or more depdencies are missing
   * @throws CircularDependencyException if a circular dependency was detected
   *
   * @since 5.0
   */
  public function get() : array;

  /**
   * Adds the given provider to the collection
   *
   * @param iDefintionsProvider $provider The provider to add to the collection
   * @return $this
   * @throws MissingDependencyException if one or more depdencies are missing
   * @throws CircularDependencyException if a circular dependency was detected
   *
   * @since 5.0
   */
  public function add(iDefintionsProvider $provider) : self;
}