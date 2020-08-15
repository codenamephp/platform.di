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

use de\codenamephp\platform\di\definitionsProvider\dependency\MissingDependencyException;
use de\codenamephp\platform\di\definitionsProvider\iDefinitionsProvider;
use InvalidArgumentException;

/**
 * Interface to build a di container.
 *
 * @since 3.0
 * @since 5.0 removed addGlobPath
 */
interface iContainerBuilder {

  /**
   * Adds definitions by a provider class. The provider must implement one of the definitionsProvider\* interfaces and the configuration will be added
   * accordingly to the container builder.
   *
   * Implementations should also check the dependencies using the iHandler. If the provider implements the iDependency interface, it is used directly for the dependency
   * check. If not, it is wrapper in the Wrapper dependency which is then used.
   *
   * @param iDefinitionsProvider $provider The provider whose definitions will be added, depending on the implemented interfaces
   *
   * @return iContainerBuilder
   * @throws MissingDependencyException if a dependency that the given provider relies on is missing (from dependencyHandler)
   * @throws InvalidArgumentException
   *
   * @since 3.0
   */
  public function addDefinitionsByProvider(definitionsProvider\iDefinitionsProvider $provider) : iContainerBuilder;
}
